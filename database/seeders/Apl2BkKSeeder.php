<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Skema;
use App\Models\APL2;

class Apl2BkKSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil skema pertama yang ada
        $skema = Skema::first();

        if (!$skema) {
            $this->command->error('Tidak ada skema yang ditemukan. Silakan jalankan SkemaSeeder terlebih dahulu.');
            return;
        }

        // Hapus soal APL2 lama
        APL2::where('skema_id', $skema->id)->delete();

        // Buat contoh soal BK/K
        $questions = [
            [
                'question_text' => 'Apa yang dimaksud dengan pemrograman?',
                'question_type' => 'bk_k_question',
                'question_options' => ['BK', 'K'],
                'bukti_isian_tes' => 'Bukti kemampuan pemrograman dasar',
                'is_bk_k_question' => true,
                'urutan' => 1
            ],
            [
                'question_text' => 'Dapatkah saya mengaplikasikan keterampilan dasar komunikasi?',
                'question_type' => 'bk_k_question',
                'question_options' => ['BK', 'K'],
                'bukti_isian_tes' => 'Bukti kemampuan komunikasi di tempat kerja',
                'is_bk_k_question' => true,
                'urutan' => 2
            ],
            [
                'question_text' => 'Apakah saya mampu mengidentifikasi proses komunikasi dengan baik?',
                'question_type' => 'bk_k_question',
                'question_options' => ['BK', 'K'],
                'bukti_isian_tes' => 'Bukti identifikasi proses komunikasi',
                'is_bk_k_question' => true,
                'urutan' => 3
            ]
        ];

        foreach ($questions as $questionData) {
            APL2::create([
                'skema_id' => $skema->id,
                'question_text' => $questionData['question_text'],
                'question_type' => $questionData['question_type'],
                'question_options' => $questionData['question_options'],
                'bukti_isian_tes' => $questionData['bukti_isian_tes'],
                'is_bk_k_question' => $questionData['is_bk_k_question'],
                'urutan' => $questionData['urutan']
            ]);
        }

        $this->command->info('Contoh soal APL2 BK/K berhasil dibuat untuk skema: ' . $skema->nama);
    }
}
