<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Response;
use Illuminate\Support\Facades\DB;

class TrimAllResponses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'response:force-trim';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Force trim all response answers (including leading/trailing whitespace and newlines)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Force trimming all response answers...');

        // Use raw SQL to trim all answers
        $updated = DB::table('responses')
            ->whereNotNull('answer_text')
            ->update([
                'answer_text' => DB::raw('TRIM(answer_text)'),
                'updated_at' => now()
            ]);

        $this->info("Done! Updated {$updated} records.");

        // Also clean comma-separated values
        $this->info('Cleaning comma-separated values...');

        $responses = Response::whereNotNull('answer_text')
            ->where('answer_text', 'LIKE', '%,%')
            ->get();

        $cleaned = 0;
        foreach ($responses as $response) {
            $parts = explode(',', $response->answer_text);
            $trimmed = array_map('trim', $parts);
            $newAnswer = implode(', ', $trimmed);

            if ($newAnswer !== $response->answer_text) {
                $response->answer_text = $newAnswer;
                $response->save();
                $cleaned++;
            }
        }

        $this->info("Cleaned {$cleaned} comma-separated responses.");
        return 0;
    }
}
