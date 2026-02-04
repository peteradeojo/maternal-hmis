<?php

namespace App\Jobs;

use App\Enums\Status;
use App\Models\StockCount;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Redis;

class StockTakeReport implements ShouldQueue
{
    use Queueable;

    public const REPORT_CACHE_KEY = "stock_generated_reports";
    protected const REPORT_LIST_KEY = "stock_take_generating_reports";

    /**
     * Create a new job instance.
     */
    public function __construct(protected int $take_id) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $client = Redis::client();

        $st = StockCount::find($this->take_id)?->load(['records']);
        if (empty($st)) {
            $client->srem(self::REPORT_LIST_KEY, $this->take_id);
            $client->hset(self::REPORT_CACHE_KEY, $this->take_id, json_encode([
                'status' => Status::failed->value,
                'message' => "No stock take with id: {$this->take_id}",
            ]));
            return;
        }

        if ($data = $client->hget(self::REPORT_CACHE_KEY, $st->id)) {
            $data = json_decode($data, true);
            if (!empty($data['file'])) {
                return;
            }
        }

        $client->sadd(self::REPORT_LIST_KEY, $this->take_id);
        $client->hset(self::REPORT_CACHE_KEY, $this->take_id, json_encode(['status' => Status::active->value]));

        $reportsDir = "stock-take-reports";
        $reportsPath = storage_path("app/$reportsDir");

        if (!is_dir($reportsPath)) {
            mkdir($reportsPath);
        }

        $filename = str_replace([" ", ":"], "_", "$reportsPath/{$st->count_date}.csv");
        $savedFilename = str_replace([" ", ":"], "_", "$reportsDir/{$st->count_date}.csv");

        $fh = fopen($filename, "w") or (function () use (&$client) {
            $client->srem(self::REPORT_LIST_KEY, $this->take_id);
            $client->hset(self::REPORT_CACHE_KEY, $this->take_id, json_encode([
                'status' => Status::failed->value,
                'message' => 'Unable to write.'
            ]));
        })();

        try {
            //code...
            $headers = ["Name", "Cost price", "System value", "Counted", "Discrepancy", "Cost Value", "Discrepancy value"];
            fputcsv($fh, $headers);

            $totalCost = $totalDelta = 0;

            $st->records->load(['item'])->each(function ($r) use (&$fh, &$totalCost, &$totalDelta) {
                $cost = $r->item->costs->first()?->cost;
                $delta = $r->counted_qty - $r->system_qty;

                $totalCost += $cost * $r->system_qty;
                $totalDelta += $cost * $delta;

                fputcsv($fh, [
                    $r->item->name,
                    $r->item->costs->first()?->cost,
                    $r->system_qty,
                    $r->counted_qty,
                    $delta,
                    $cost * $r->system_qty,
                    $cost * $delta,
                ]);
            });

            fputcsv($fh, ['', '', '', '', '', $totalCost, $totalDelta]);
            $client->hset(self::REPORT_CACHE_KEY, $this->take_id, json_encode([
                'status' => Status::completed->value,
                'file' => $savedFilename,
                'timestamp' => now(),
            ]));
        } catch (\Throwable $th) {
            report($th);
            $client->hset(self::REPORT_CACHE_KEY, $this->take_id, json_encode([
                'status' => Status::failed->value,
                'message' => $th->getMessage()
            ]));
        } finally {
            dump("Closing file stream: $fh");
            fclose($fh);
            $client->srem(self::REPORT_LIST_KEY, $this->take_id);
        }
    }

    public static function getReportStatus($id)
    {
        $client = Redis::client();
        if ($data = $client->hget(self::REPORT_CACHE_KEY, $id)) {
            return json_decode($data, true)['status'];
        }

        if ($client->sismember(self::REPORT_LIST_KEY, $id)) {
            return Status::pending->value;
        }

        return null;
    }

    public static function getReportFile($id)
    {
        $client = Redis::client();

        $data = $client->hget(self::REPORT_CACHE_KEY, $id);
        return @(json_decode($data, true)['file']);
    }
}
