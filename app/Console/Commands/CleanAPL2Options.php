<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\APL2;

class CleanAPL2Options extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apl2:clean-options';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean whitespace from APL2 question options';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning APL2 question options...');

        $apl2Records = APL2::whereNotNull('question_options')->get();
        $cleaned = 0;

        foreach ($apl2Records as $apl2) {
            $originalOptions = $apl2->question_options;
            $cleanedOptions = null;

            if (is_array($originalOptions)) {
                // Trim each option in array
                $cleanedOptions = array_map('trim', $originalOptions);
            } elseif (is_string($originalOptions)) {
                // If it's a string, split, trim, and rejoin
                $options = explode(',', $originalOptions);
                $trimmed = array_map('trim', $options);
                $cleanedOptions = $trimmed;
            }

            // Only update if there's a difference
            if ($cleanedOptions !== null && $originalOptions != $cleanedOptions) {
                $apl2->question_options = $cleanedOptions;
                $apl2->save();
                $cleaned++;
                $this->line("Cleaned #{$apl2->id}: {$apl2->question_text}");
                $this->line("  Before: " . json_encode($originalOptions));
                $this->line("  After:  " . json_encode($cleanedOptions));
            }
        }

        $this->info("Done! Cleaned {$cleaned} records.");
        return 0;
    }
}
