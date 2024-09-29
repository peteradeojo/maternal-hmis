<?php

namespace App\Jobs;

use App\Models\PatientImaging;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class UploadPatientScans implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public PatientImaging $scan)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->scan->refresh();
        $filepath = storage_path('app/' . $this->scan->path);
        $this->scan->path = cloudinary()->uploadFile($filepath, [
            'folder' => 'maternalchild/radiology/',
        ])->getSecurePath();

        $this->scan->save();

        Storage::delete($filepath);
    }
}
