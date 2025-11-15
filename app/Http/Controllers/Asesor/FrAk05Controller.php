<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\PendaftaranUjikom;
use App\Models\TemplateMaster;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;

class FrAk05Controller extends Controller
{
    use MenuTrait;

    /**
     * Helper: Get jadwal dengan verifikasi skema asesor
     */
    private function getJadwalForAsesor($jadwalId)
    {
        $asesor = Auth::user();
        
        // Get skema IDs yang dimiliki asesor ini
        $skemaIds = DB::table('asesor_skema')
            ->where('asesor_id', $asesor->id)
            ->pluck('skema_id');

        // Cari jadwal dengan verifikasi skema
        return Jadwal::where('id', $jadwalId)
            ->whereIn('skema_id', $skemaIds)
            ->with(['skema', 'tuk'])
            ->firstOrFail();
    }

    /**
     * Show form for FR AK 05 with pre-filled data
     */
    public function showForm($jadwalId)
    {
        $lists = $this->getMenuListAsesor('hasil-ujikom');
        $activeMenu = 'hasil-ujikom';

        // Get jadwal with relationships dan verifikasi skema asesor
        $jadwal = $this->getJadwalForAsesor($jadwalId);

        // Get all asesi for this jadwal
        $asesiList = PendaftaranUjikom::with(['asesi', 'pendaftaran'])
            ->where('jadwal_id', $jadwalId)
            ->whereIn('status', [4, 5]) // Only assessed asesi
            ->get();

        // Check if all asesi have been assessed
        $totalAsesi = PendaftaranUjikom::where('jadwal_id', $jadwalId)->count();
        $assessedAsesi = $asesiList->count();

        if ($totalAsesi != $assessedAsesi) {
            return redirect()->back()->with('error', 'Belum semua asesi dinilai. Silakan nilai semua asesi terlebih dahulu.');
        }

        // Get FR AK 05 template for this skema
        $template = TemplateMaster::where('skema_id', $jadwal->skema_id)
            ->where('tipe_template', 'FR_AK_05')
            ->where('is_active', true)
            ->first();

        if (!$template) {
            return redirect()->back()->with('error', 'Template FR AK 05 belum diupload untuk skema ini. Silakan hubungi admin.');
        }

        // Count kompeten and tidak kompeten
        $kompeten = $asesiList->where('status', 5)->count();
        $tidakKompeten = $asesiList->where('status', 4)->count();

        // Get custom variables from template and find signature field
        $signatureFieldName = null;
        $customVariables = collect($template->custom_variables ?? [])
            ->filter(function ($variable) use (&$signatureFieldName) {
                // Find signature field name
                if (($variable['type'] ?? 'text') === 'signature_pad') {
                    $signatureFieldName = $variable['name'] ?? null;
                    return false; // Exclude from custom variables list (will be rendered separately)
                }
                return true;
            })
            ->values()
            ->toArray();

        // Prepare auto-filled data
        $autoFilledData = [
            'asesi_list' => $asesiList->map(function ($item) {
                return [
                    'nama' => $item->asesi->name,
                    'nim' => $item->asesi->nim,
                    'status' => $item->status == 5 ? 'Kompeten' : 'Tidak Kompeten',
                ];
            })->toArray(),
            'asesi_kompeten' => $kompeten,
            'asesi_tidak_kompeten' => $tidakKompeten,
            'total_asesi' => $totalAsesi,
            'skema_nama' => $jadwal->skema->nama,
            'skema_kode' => $jadwal->skema->kode,
            'tanggal_ujian' => $jadwal->tanggal_ujian,
            'tuk_nama' => $jadwal->tuk->nama,
            'tuk_alamat' => $jadwal->tuk->alamat,
            'asesor_nama' => Auth::user()->name,
        ];

        return view('components.pages.asesor.fr-ak-05.form', compact(
            'lists',
            'activeMenu',
            'jadwal',
            'asesiList',
            'template',
            'customVariables',
            'autoFilledData',
            'kompeten',
            'tidakKompeten',
            'signatureFieldName'
        ));
    }

    /**
     * Generate FR AK 05 document
     */
    public function generate(Request $request, $jadwalId)
    {
        try {
            // Get jadwal with relationships dan verifikasi skema asesor
            $jadwal = $this->getJadwalForAsesor($jadwalId);
            
            // Get template to find signature field name
            $template = TemplateMaster::where('skema_id', $jadwal->skema_id)
                ->where('tipe_template', 'FR_AK_05')
                ->where('is_active', true)
                ->first();

            if (!$template) {
                return redirect()->back()->with('error', 'Template FR AK 05 tidak ditemukan.');
            }
            
            // Find signature field name from custom variables
            $signatureFieldName = null;
            $customVariables = $template->custom_variables ?? [];
            foreach ($customVariables as $variable) {
                if (($variable['type'] ?? 'text') === 'signature_pad') {
                    $signatureFieldName = $variable['name'] ?? null;
                    break;
                }
            }
            
            // Validate signature dynamically
            if ($signatureFieldName) {
                $request->validate([
                    $signatureFieldName => 'required',
                ], [
                    $signatureFieldName . '.required' => 'Tanda tangan asesor wajib diisi.',
                ]);
            }

            // Get all assessed asesi for this jadwal
            $asesiList = PendaftaranUjikom::with(['asesi', 'pendaftaran'])
                ->where('jadwal_id', $jadwalId)
                ->whereIn('status', [4, 5])
                ->get();

            // Get template file path
            $templatePath = storage_path('app/public/' . $template->file_path);

            if (!file_exists($templatePath)) {
                return redirect()->back()->with('error', 'File template FR AK 05 tidak ditemukan.');
            }

            // Load template using PhpWord
            $templateProcessor = new TemplateProcessor($templatePath);

            // Count kompeten and tidak kompeten
            $kompeten = $asesiList->where('status', 5)->count();
            $tidakKompeten = $asesiList->where('status', 4)->count();

            // Prepare basic auto-filled data
            $autoFilledData = [
                'skema.judul' => $jadwal->skema->nama,
                'skema.nomor' => $jadwal->skema->kode ?? '',
                'tuk' => $jadwal->tuk->nama,
                'nama_asesor' => Auth::user()->name,
                'tanggal' => now()->format('d-m-Y'),
                'asesi_kompeten' => $kompeten,
                'asesi_tidak_kompeten' => $tidakKompeten,
                'total_asesi' => $asesiList->count(),
                'jadwal.tanggal_ujian' => $jadwal->tanggal_ujian,
                'jadwal.tuk.alamat' => $jadwal->tuk->alamat ?? '',
                'system.tanggal_generate' => now()->format('d-m-Y'),
                'system.waktu_generate' => now()->format('H:i:s'),
            ];

            // Replace auto-filled variables
            foreach ($autoFilledData as $key => $value) {
                $templateProcessor->setValue($key, $value);
            }

            // Clone row untuk tabel asesi (jika template punya placeholder ${nama_asesi})
            try {
                $templateProcessor->cloneRow('nama_asesi', $asesiList->count());

                $rowNumber = 1;
                foreach ($asesiList as $index => $asesiItem) {
                    $templateProcessor->setValue('no#' . $rowNumber, $rowNumber);
                    $templateProcessor->setValue('nama_asesi#' . $rowNumber, $asesiItem->asesi->name);

                    // Set checkbox K atau BK berdasarkan status
                    if ($asesiItem->status == 5) {
                        // Kompeten - centang K
                        $templateProcessor->setValue('checkbox_k#' . $rowNumber, '☑');
                        $templateProcessor->setValue('checkbox_bk#' . $rowNumber, '☐');
                    } else {
                        // Tidak Kompeten - centang BK
                        $templateProcessor->setValue('checkbox_k#' . $rowNumber, '☐');
                        $templateProcessor->setValue('checkbox_bk#' . $rowNumber, '☑');
                    }

                    // Keterangan dari custom field atau kosong
                    $keterangan = $request->input('keterangan_' . $asesiItem->id, '-');
                    $templateProcessor->setValue('keterangan#' . $rowNumber, $keterangan);

                    $rowNumber++;
                }
            } catch (\Exception $e) {
                // Jika tidak ada placeholder nama_asesi (template tidak pakai tabel), skip
                \Log::info('Template FR AK 05 tidak menggunakan tabel dinamis: ' . $e->getMessage());
            }

            // Get custom variables from request and replace them
            // Also find signature field name from custom variables
            $customVariables = $template->custom_variables ?? [];
            $signatureFieldName = null;
            
            foreach ($customVariables as $variable) {
                $varName = $variable['name'] ?? '';
                $varType = $variable['type'] ?? 'text';
                $varValue = $request->input($varName, '');

                // If this is signature_pad type, store field name and skip text replacement
                if ($varType === 'signature_pad') {
                    $signatureFieldName = $varName;
                    \Log::info('Found signature field from custom variables: ' . $varName);
                    continue; // Don't setValue for signature, will be processed as image below
                }

                // For non-signature fields, do normal text replacement
                if ($varName) {
                    $templateProcessor->setValue($varName, $varValue);
                }
            }

            // Process signature field dynamically from custom variables
            if ($signatureFieldName && $request->has($signatureFieldName) && !empty($request->input($signatureFieldName))) {
                try {
                    // Decode base64 signature
                    $signatureData = $request->input($signatureFieldName);

                    // Remove data:image/png;base64, prefix if exists
                    if (strpos($signatureData, 'data:image') !== false) {
                        $signatureData = explode(',', $signatureData)[1];
                    }

                    $signatureImage = base64_decode($signatureData);

                    // Save signature to public storage
                    $signatureFileName = 'ttd_' . Auth::user()->id . '_' . time() . '.png';
                    $signaturePath = 'signatures/' . $signatureFileName;
                    $fullSignaturePath = storage_path('app/public/' . $signaturePath);

                    // Create directory if not exists
                    if (!file_exists(dirname($fullSignaturePath))) {
                        mkdir(dirname($fullSignaturePath), 0755, true);
                    }

                    file_put_contents($fullSignaturePath, $signatureImage);

                    \Log::info('Signature saved to: ' . $fullSignaturePath);

                    // Try to replace signature in template as image
                    try {
                        $templateProcessor->setImageValue($signatureFieldName, [
                            'path' => $fullSignaturePath,
                            'width' => 150,
                            'height' => 50,
                            'ratio' => false
                        ]);
                        \Log::info('Signature inserted successfully with placeholder: ' . $signatureFieldName);
                    } catch (\Exception $e) {
                        \Log::warning($signatureFieldName . ' image placeholder not found: ' . $e->getMessage());

                        // Fallback: use text placeholder with note
                        try {
                            $templateProcessor->setValue($signatureFieldName, '[Tanda Tangan Digital - ' . Auth::user()->name . ']');
                            \Log::info('Using text fallback for signature field: ' . $signatureFieldName);
                        } catch (\Exception $e2) {
                            \Log::error('Signature placeholder replacement failed: ' . $e2->getMessage());
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Error processing signature: ' . $e->getMessage());
                    \Log::error('Signature error trace: ' . $e->getTraceAsString());
                }
            } else {
                \Log::warning('No signature field found in custom variables or signature data is empty');
            }

            // Generate output filename
            $outputFileName = 'FR_AK_05_' . $jadwal->skema->kode . '_' . now()->format('Ymd_His') . '.docx';
            $outputPath = 'generated/fr_ak_05/' . $outputFileName;
            $fullOutputPath = storage_path('app/public/' . $outputPath);

            // Create directory if not exists
            $outputDir = dirname($fullOutputPath);
            if (!file_exists($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            // Save the generated document
            $templateProcessor->saveAs($fullOutputPath);

            // NOTE: Signature file is kept in storage/app/public/signatures/ for audit purposes
            // It will not be deleted automatically

            \Log::info('FR AK 05 generated successfully: ' . $fullOutputPath);

            // Download the generated file
            return response()->download($fullOutputPath, $outputFileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])->deleteFileAfterSend(false);
        } catch (\Exception $e) {
            \Log::error('FR AK 05 Generation Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat generate FR AK 05: ' . $e->getMessage());
        }
    }
}
