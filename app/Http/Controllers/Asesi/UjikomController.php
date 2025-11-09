<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\APL2;
use App\Models\Pendaftaran;
use App\Models\PendaftaranUjikom;
use App\Models\Response;
use App\Models\TemplateMaster;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UjikomController extends Controller
{
    use MenuTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $asesi = Auth::user();
        $lists = $this->getMenuListAsesi('ujikom');
        $pendaftaran = Pendaftaran::where('user_id', $asesi->id)
            ->with(['pendaftaranUjikom', 'jadwal', 'jadwal.skema', 'jadwal.tuk'])
            ->get();

        return view('components.pages.asesi.ujikom.list', compact('lists', 'pendaftaran'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $id)
    {
        // Handle file uploads and convert checkbox arrays
        $answers = $request->input('answers', []);
        $files = $request->file('files', []);

        foreach ($answers as $apl2_id => $answer) {
            if (is_array($answer)) {
                // Convert array to comma-separated string and trim each value
                $trimmedAnswers = array_map('trim', $answer);
                $answers[$apl2_id] = implode(', ', $trimmedAnswers);
            } else {
                // Trim string answers as well (text, textarea, etc)
                $answers[$apl2_id] = trim($answer);
            }
        }

        // Handle file uploads
        foreach ($files as $apl2_id => $file) {
            if ($file && $file->isValid()) {
                // Validate file size (max 2MB)
                if ($file->getSize() > 2048000) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['answers.' . $apl2_id => 'File terlalu besar. Maksimal 2MB.']);
                }

                $fileName = time() . '_' . $apl2_id . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('ujikom_files', $fileName, 'public');
                $answers[$apl2_id] = $filePath;
            }
        }

        // Replace the answers in the request
        $request->merge(['answers' => $answers]);

        $rules = [];

        // Validasi jawaban soal APL2
        foreach ($answers as $apl2_id => $answer) {
            $rules['answers.' . $apl2_id] = 'required|string';
        }

        $validated = $request->validate($rules);

        // Simpan jawaban soal APL2
        foreach ($validated['answers'] as $apl2_id => $answer_text) {
            Response::updateOrCreate(
                [
                    'pendaftaran_id' => $id,
                    'apl2_id' => $apl2_id,
                ],
                [
                    'answer_text' => $answer_text,
                ]
            );
        }

        // Update status ujikom
        $pendaftaranUjikom = PendaftaranUjikom::where('pendaftaran_id', $id)
            ->where('asesi_id', Auth::user()->id)
            ->first();

        if ($pendaftaranUjikom) {
            $pendaftaranUjikom->status = 3;
            $pendaftaranUjikom->save();
        }

        return redirect()->route('asesi.ujikom.index')->with('success', 'Jawaban berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $asesi = Auth::user();
        $pendaftaran = Pendaftaran::where('id', $id)
            ->where('user_id', $asesi->id)
            ->first();

        if (!$pendaftaran) {
            return redirect()->route('asesi.ujikom.index')->with('error', 'Pendaftaran tidak ditemukan.');
        }

        // Ambil soal APL2 untuk skema ini
        $apl2 = APL2::where('skema_id', $pendaftaran->skema_id)
            ->orderBy('urutan', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        $lists = $this->getMenuListAsesi('ujikom');

        // Update status ujikom
        $pendaftaranUjikom = PendaftaranUjikom::where('pendaftaran_id', $pendaftaran->id)
            ->where('asesi_id', Auth::user()->id)
            ->first();

        if ($pendaftaranUjikom) {
            $pendaftaranUjikom->status = 2;
            $pendaftaranUjikom->save();
        }

        // Update status pendaftaran
        $pendaftaran->status = 5;
        $pendaftaran->save();

        return view('components.pages.asesi.ujikom.show', compact('pendaftaran', 'lists', 'apl2'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
