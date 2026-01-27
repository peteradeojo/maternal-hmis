<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("DROP FUNCTION IF EXISTS rebuild_inventory_balances();");
        DB::unprepared("
        CREATE OR REPLACE FUNCTION rebuild_inventory_balances()
        RETURNS void
        LANGUAGE plpgsql
        AS $$
        BEGIN
            TRUNCATE inventory_balances RESTART IDENTITY;

            INSERT INTO inventory_balances (
                item_id, location_id, lot_id,
                qty_on_hand, last_updated,
                created_at, updated_at
            )
            SELECT
                item_id,
                location_id,
                lot_id,
                SUM(qty_delta) AS qty_on_hand,
                MAX(ts) AS last_updated,
                NOW(), NOW()
            FROM (
                -- INBOUND: to_location_id is REAL (not virtual)
                SELECT
                    item_id,
                    to_location_id AS location_id,
                    lot_id,
                    quantity AS qty_delta,
                    created_at AS ts
                FROM stock_transactions
                WHERE to_location_id NOT IN (0, 1000)

                UNION ALL

                -- OUTBOUND: from_location_id is REAL (not virtual)
                SELECT
                    item_id,
                    from_location_id AS location_id,
                    lot_id,
                    -quantity AS qty_delta,
                    created_at AS ts
                FROM stock_transactions
                WHERE from_location_id NOT IN (0, 1000)
            ) AS tx
            GROUP BY item_id, location_id, lot_id;
        END;
        $$;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
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
        $$;');
    }
};
