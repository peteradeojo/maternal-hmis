<?php

namespace App\Jobs;

use App\Models\Visit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use function Spatie\LaravelPdf\Support\pdf;

class GenerateVisitReport implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private Visit $visit) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $html = pdf()->view('visit-report', [
            'visit' => $this->visit,
        ])
        ->save("{$this->visit->patient->name}.pdf");
    }
}
