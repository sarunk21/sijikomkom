<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Response;

class CleanResponseAnswers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'response:clean-answers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean whitespace from response answers (checkbox values)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning response answers...');

        $responses = Response::whereNotNull('answer_text')->get();
        $cleaned = 0;

        foreach ($responses as $response) {
            $originalAnswer = $response->answer_text;
            $cleanedAnswer = $originalAnswer;

            // Check if it's a comma-separated answer (checkbox)
            if (strpos($originalAnswer, ',') !== false) {
                // Split, trim each part, and rejoin
                $parts = explode(',', $originalAnswer);
                $trimmedParts = array_map('trim', $parts);
                $cleanedAnswer = implode(', ', $trimmedParts);
            } else {
                // For regular answers, just trim whitespace
                $cleanedAnswer = trim($originalAnswer);
            }

            // Only update if there's a difference
            if ($originalAnswer !== $cleanedAnswer) {
                $response->answer_text = $cleanedAnswer;
                $response->save();
                $cleaned++;
                $this->line("Cleaned Response #{$response->id}");
                $this->line("  Before: '{$originalAnswer}'");
                $this->line("  After:  '{$cleanedAnswer}'");
            }
        }

        $this->info("Done! Cleaned {$cleaned} records.");
        return 0;
    }
}
