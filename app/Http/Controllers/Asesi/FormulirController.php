<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\BankSoal;
use App\Models\FormulirResponse;
use App\Models\Jadwal;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FormulirController extends Controller
{
    use MenuTrait;

    /**
     * Tampilkan list formulir yang harus diisi asesi
     */
    public function index($jadwalId)
    {
        $lists = $this->getMenuListAsesi('ujian');
        $activeMenu = 'ujian';

        // Pastikan jadwal masih berlangsung (status 3)
        $jadwal = Jadwal::where('id', $jadwalId)
            ->with('skema')
            ->first();
        
        if (!$jadwal) {
            return redirect()->route('asesi.ujikom.index')
                ->with('error', 'Jadwal tidak ditemukan.');
        }
        
        if ($jadwal->status != 3) {
            return redirect()->route('asesi.ujikom.index')
                ->with('error', 'Jadwal ujian sudah selesai. Anda tidak dapat lagi mengisi formulir.');
        }
        
        $userId = Auth::id();

        // Get formulir untuk asesi (target = 'asesi')
        $bankSoals = BankSoal::where('skema_id', $jadwal->skema_id)
            ->where('target', 'asesi')
            ->where('is_active', true)
            ->get();

        // Get status pengisian untuk setiap formulir
        $responses = FormulirResponse::where('jadwal_id', $jadwalId)
            ->where('user_id', $userId)
            ->get()
            ->keyBy('bank_soal_id');

        return view('components.pages.asesi.formulir.index', compact(
            'lists',
            'activeMenu',
            'jadwal',
            'bankSoals',
            'responses'
        ));
    }

    /**
     * Tampilkan form untuk mengisi formulir
     */
    public function fill($jadwalId, $bankSoalId)
    {
        $lists = $this->getMenuListAsesi('ujian');
        $activeMenu = 'ujian';

        // Pastikan jadwal masih berlangsung (status 3)
        $jadwal = Jadwal::where('id', $jadwalId)
            ->with('skema')
            ->first();
        
        if (!$jadwal) {
            return redirect()->route('asesi.ujikom.index')
                ->with('error', 'Jadwal tidak ditemukan.');
        }
        
        if ($jadwal->status != 3) {
            return redirect()->route('asesi.ujikom.index')
                ->with('error', 'Jadwal ujian sudah selesai. Anda tidak dapat lagi mengisi formulir.');
        }
        
        $bankSoal = BankSoal::findOrFail($bankSoalId);
        $userId = Auth::id();

        // Get or create response
        $response = FormulirResponse::firstOrCreate(
            [
                'jadwal_id' => $jadwalId,
                'user_id' => $userId,
                'bank_soal_id' => $bankSoalId,
            ],
            [
                'status' => 'draft',
                'asesi_responses' => [],
            ]
        );

        // Get custom fields untuk asesi (role = asesi atau both)
        // Gunakan field_configurations jika ada, jika tidak gunakan custom_variables sebagai fallback
        $fieldSource = $bankSoal->field_configurations ?? $bankSoal->custom_variables ?? [];
        $customFields = collect($fieldSource)
            ->filter(function ($field) {
                return in_array($field['role'] ?? 'asesi', ['asesi', 'both']);
            });

        return view('components.pages.asesi.formulir.fill', compact(
            'lists',
            'activeMenu',
            'jadwal',
            'bankSoal',
            'response',
            'customFields'
        ));
    }

    /**
     * Save draft jawaban
     */
    public function saveDraft(Request $request, $jadwalId, $bankSoalId)
    {
        $userId = Auth::id();

        // Pastikan jadwal masih berlangsung (status 3)
        $jadwal = Jadwal::where('id', $jadwalId)->first();
        
        if (!$jadwal || $jadwal->status != 3) {
            return redirect()->route('asesi.ujikom.index')
                ->with('error', 'Jadwal ujian sudah selesai. Anda tidak dapat lagi menyimpan jawaban.');
        }

        $response = FormulirResponse::where('jadwal_id', $jadwalId)
            ->where('user_id', $userId)
            ->where('bank_soal_id', $bankSoalId)
            ->firstOrFail();

        $response->update([
            'asesi_responses' => $request->responses,
            'status' => 'draft',
        ]);

        return redirect()->back()->with('success', 'Draft berhasil disimpan');
    }

    /**
     * Submit jawaban final
     */
    public function submit(Request $request, $jadwalId, $bankSoalId)
    {
        $userId = Auth::id();
        
        // Pastikan jadwal masih berlangsung (status 3)
        $jadwal = Jadwal::where('id', $jadwalId)->first();
        
        if (!$jadwal || $jadwal->status != 3) {
            return redirect()->route('asesi.ujikom.index')
                ->with('error', 'Jadwal ujian sudah selesai. Anda tidak dapat lagi submit formulir.');
        }
        
        $bankSoal = BankSoal::findOrFail($bankSoalId);

        // Validasi required fields
        // Gunakan field_configurations jika ada, jika tidak gunakan custom_variables sebagai fallback
        $fieldSource = $bankSoal->field_configurations ?? $bankSoal->custom_variables ?? [];
        $customFields = collect($fieldSource)
            ->filter(function ($field) {
                return in_array($field['role'] ?? 'asesi', ['asesi', 'both']);
            });

        $rules = [];
        foreach ($customFields as $index => $field) {
            if ($field['required'] ?? false) {
                $rules["responses.{$field['name']}"] = 'required';
            }
        }

        $request->validate($rules, [
            'required' => ':attribute wajib diisi',
        ]);

        $response = FormulirResponse::where('jadwal_id', $jadwalId)
            ->where('user_id', $userId)
            ->where('bank_soal_id', $bankSoalId)
            ->firstOrFail();

        $response->update([
            'asesi_responses' => $request->responses,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('asesi.formulir.index', $jadwalId)
            ->with('success', 'Formulir berhasil disubmit');
    }

    /**
     * View jawaban yang sudah disubmit (read-only)
     */
    public function view($jadwalId, $bankSoalId)
    {
        $lists = $this->getMenuListAsesi('ujian');
        $activeMenu = 'ujian';

        $jadwal = Jadwal::with('skema')->findOrFail($jadwalId);
        $bankSoal = BankSoal::findOrFail($bankSoalId);
        $userId = Auth::id();

        $response = FormulirResponse::where('jadwal_id', $jadwalId)
            ->where('user_id', $userId)
            ->where('bank_soal_id', $bankSoalId)
            ->firstOrFail();

        // Gunakan field_configurations jika ada, jika tidak gunakan custom_variables sebagai fallback
        $fieldSource = $bankSoal->field_configurations ?? $bankSoal->custom_variables ?? [];
        $customFields = collect($fieldSource)
            ->filter(function ($field) {
                return in_array($field['role'] ?? 'asesi', ['asesi', 'both']);
            });

        return view('components.pages.asesi.formulir.view', compact(
            'lists',
            'activeMenu',
            'jadwal',
            'bankSoal',
            'response',
            'customFields'
        ));
    }
}
