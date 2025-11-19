<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrasi data APL2 ke Bank Soal
        $this->migrateApl2ToBankSoal();

        // Migrasi data Responses ke Formulir Responses
        $this->migrateResponsesToFormulirResponses();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback tidak perlu karena data asli masih ada di tabel lama
        // Nanti akan dihapus di migration terpisah setelah verifikasi
    }

    /**
     * Migrasi APL2 ke Bank Soal
     */
    private function migrateApl2ToBankSoal(): void
    {
        // Group APL2 questions by skema_id
        $skemas = DB::table('apl2')
            ->select('skema_id')
            ->distinct()
            ->get();

        foreach ($skemas as $skema) {
            // Get all questions for this skema
            $questions = DB::table('apl2')
                ->where('skema_id', $skema->skema_id)
                ->orderBy('urutan')
                ->get();

            if ($questions->isEmpty()) {
                continue;
            }

            // Create field configurations from APL2 questions
            $fieldConfigurations = [];
            foreach ($questions as $index => $question) {
                $fieldName = 'pertanyaan_' . ($index + 1);

                $fieldConfigurations[] = [
                    'name' => $fieldName,
                    'label' => $question->question_text,
                    'type' => $this->mapQuestionType($question->question_type),
                    'options' => $question->question_options ? json_decode($question->question_options, true) : null,
                    'required' => true,
                    'role' => 'asesi', // APL2 diisi oleh asesi
                    'order' => $question->urutan,
                    'validation' => $question->is_bk_k_question ? 'required' : null,
                ];
            }

            // Get skema name
            $skemaData = DB::table('skema')->where('id', $skema->skema_id)->first();
            $skemaName = $skemaData ? $skemaData->nama : 'Unknown';

            // Check if Bank Soal for this skema already exists
            $existingBankSoal = DB::table('bank_soal')
                ->where('skema_id', $skema->skema_id)
                ->where('tipe', 'APL2')
                ->first();

            if (!$existingBankSoal) {
                // Insert into bank_soal
                DB::table('bank_soal')->insert([
                    'skema_id' => $skema->skema_id,
                    'nama' => 'APL 2 - ' . $skemaName,
                    'tipe' => 'APL2',
                    'target' => 'asesi',
                    'file_path' => null, // APL2 tidak punya template file
                    'original_filename' => null,
                    'is_active' => true,
                    'keterangan' => 'Migrated from APL2 table',
                    'variables' => json_encode([]),
                    'field_configurations' => json_encode($fieldConfigurations),
                    'field_mappings' => json_encode([]),
                    'custom_variables' => json_encode([]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Migrasi Responses ke Formulir Responses
     */
    private function migrateResponsesToFormulirResponses(): void
    {
        // Get all responses grouped by pendaftaran_id
        $pendaftarans = DB::table('responses')
            ->select('pendaftaran_id')
            ->distinct()
            ->get();

        foreach ($pendaftarans as $pend) {
            // Get pendaftaran data to get jadwal_id and skema_id
            $pendaftaran = DB::table('pendaftaran')
                ->where('id', $pend->pendaftaran_id)
                ->first();

            if (!$pendaftaran) {
                continue;
            }

            // Get Bank Soal for this skema with type APL2
            $bankSoal = DB::table('bank_soal')
                ->where('skema_id', $pendaftaran->skema_id)
                ->where('tipe', 'APL2')
                ->first();

            if (!$bankSoal) {
                continue;
            }

            // Get all responses for this pendaftaran
            $responses = DB::table('responses')
                ->join('apl2', 'responses.apl2_id', '=', 'apl2.id')
                ->where('responses.pendaftaran_id', $pend->pendaftaran_id)
                ->select('responses.*', 'apl2.urutan')
                ->orderBy('apl2.urutan')
                ->get();

            // Build asesi_responses
            $asesiResponses = [];
            foreach ($responses as $index => $response) {
                $fieldName = 'pertanyaan_' . ($index + 1);
                $asesiResponses[$fieldName] = $response->answer_text;
            }

            // Check if FormulirResponse already exists
            $existingFormulir = DB::table('formulir_responses')
                ->where('jadwal_id', $pendaftaran->jadwal_id)
                ->where('user_id', $pendaftaran->user_id)
                ->where('bank_soal_id', $bankSoal->id)
                ->first();

            if (!$existingFormulir) {
                // Insert into formulir_responses
                DB::table('formulir_responses')->insert([
                    'jadwal_id' => $pendaftaran->jadwal_id,
                    'user_id' => $pendaftaran->user_id,
                    'bank_soal_id' => $bankSoal->id,
                    'asesi_responses' => json_encode($asesiResponses),
                    'asesor_responses' => json_encode([]),
                    'asesor_validations' => json_encode([]),
                    'is_asesor_completed' => false,
                    'status' => 'submitted', // Karena sudah dijawab
                    'catatan_asesor' => null,
                    'submitted_at' => $responses->first()->created_at,
                    'reviewed_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Map APL2 question type to Bank Soal field type
     */
    private function mapQuestionType(string $type): string
    {
        $mapping = [
            'text' => 'text',
            'textarea' => 'textarea',
            'checkbox' => 'checkbox',
            'radio' => 'radio',
            'select' => 'select',
            'number' => 'number',
            'email' => 'email',
            'date' => 'date',
            'file' => 'file',
        ];

        return $mapping[$type] ?? 'text';
    }
};
