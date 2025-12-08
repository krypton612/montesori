<?php

namespace App\Jobs;

use App\Models\Curso;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GeneratePlanEvaluacionesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * Create a new job instance.
     */
    public function __construct(public Curso $curso)
    {
        $this->delay(now()->addSeconds(1));
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

    }
}
