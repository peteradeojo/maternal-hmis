<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared("
        CREATE OR REPLACE FUNCTION apply_stock_transaction(
            p_item_id BIGINT,
            p_location_id BIGINT,
            p_lot_id BIGINT,
            p_qty_delta NUMERIC
        ) RETURNS NUMERIC
         LANGUAGE plpgsql
         AS $$
         DECLARE v_new_qty NUMERIC;
         BEGIN
            -- Insert if not exists
            INSERT INTO inventory_balances (item_id, location_id, lot_id, qty_on_hand, last_updated, created_at, updated_at)
            VALUES (p_item_id, p_location_id, p_lot_id, 0, NOW(), NOW(), NOW())
            ON CONFLICT (item_id, location_id, lot_id) DO NOTHING;

            -- Update qty_on_hand atomically
            UPDATE inventory_balances
            SET qty_on_hand = qty_on_hand + p_qty_delta,
                last_updated = NOW(),
                updated_at = NOW()
            WHERE item_id = p_item_id
                AND location_id = p_location_id
                AND (lot_id IS NOT DISTINCT FROM p_lot_id)
            RETURNING qty_on_hand INTO v_new_qty;

            RETURN v_new_qty;
        END;
        $$;
        ");

        DB::unprepared("
        CREATE OR REPLACE FUNCTION trg_after_stock_transaction()
        RETURNS TRIGGER
        LANGUAGE plpgsql
        AS $$
        BEGIN
            -- Inbound: from virtual location → real location
            IF NEW.to_location_id NOT IN (0, 1000) THEN
                PERFORM apply_stock_transaction(
                    NEW.item_id,
                    NEW.to_location_id,
                    NEW.lot_id,
                    NEW.quantity
                );
            END IF;

            -- Outbound: from real location → virtual location
            IF NEW.from_location_id NOT IN (0, 1000) THEN
                PERFORM apply_stock_transaction(
                    NEW.item_id,
                    NEW.from_location_id,
                    NEW.lot_id,
                    -NEW.quantity
                );
            END IF;

            RETURN NEW;
        END;
        $$;
        ");

        DB::statement("
        CREATE TRIGGER after_stock_transaction
        AFTER INSERT ON stock_transactions
        FOR EACH ROW
        EXECUTE FUNCTION trg_after_stock_transaction();
        ");


        DB::unprepared('
        -- =========================
        -- 4. Full rebuild function
        -- =========================
        CREATE OR REPLACE FUNCTION rebuild_inventory_balances()
        RETURNS void
        LANGUAGE plpgsql
        AS $$
        BEGIN
            -- Clear all existing balances
            TRUNCATE inventory_balances;

            -- Recompute balances from the entire transaction history
            INSERT INTO inventory_balances (item_id, location_id, lot_id, qty_on_hand, last_updated)
            SELECT
                item_id,
                COALESCE(to_location_id, 0) AS location_id,  -- virtual inbound location 0
                lot_id,
                SUM(
                    CASE WHEN to_location_id IS NOT NULL THEN quantity ELSE 0 END
                    - CASE WHEN from_location_id IS NOT NULL THEN quantity ELSE 0 END
                ) AS qty_on_hand,
                MAX(created_at) AS last_updated
            FROM stock_transactions
            GROUP BY item_id, COALESCE(to_location_id, 0), lot_id;
        END;
        $$;
');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TRIGGER IF EXISTS after_stock_transaction ON stock_transactions;");
        DB::statement("DROP FUNCTION IF EXISTS trg_after_stock_transaction();");
        DB::statement("DROP FUNCTION IF EXISTS apply_stock_transaction (BIGINT, BIGINT, BIGINT, NUMERIC);");
        DB::statement("DROP FUNCTION IF EXISTS rebuild_inventory_balances();");
    }
};
