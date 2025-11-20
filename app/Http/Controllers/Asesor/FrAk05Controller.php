<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\AsesiPenilaian;
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
     * Helper: Get field value from database field path (e.g., "skema.nama", "asesor.name")
     */
    private function getFieldValueFromPath($fieldPath, $jadwal, $asesor, $skema)
    {
        // Split the field path (e.g., "skema.nama" -> ["skema", "nama"])
        $parts = explode('.', $fieldPath);

        if (count($parts) < 2) {
            return null;
        }

        $entity = $parts[0]; // e.g., "skema", "asesor", "jadwal"
        $field = $parts[1];  // e.g., "nama", "name", "tanggal_ujian"

        switch ($entity) {
            case 'skema':
                return $skema->{$field} ?? '';

            case 'asesor':
                if ($field === 'tanggal_lahir' && $asesor->{$field}) {
                    // Format date if it's a date field
                    if (is_string($asesor->{$field})) {
                        return $asesor->{$field};
                    }
                    return $asesor->{$field}->format('d/m/Y');
                }
                return $asesor->{$field} ?? '';

            case 'jadwal':
                // Handle nested fields like "jadwal.tuk.nama"
                if (count($parts) === 3 && $parts[1] === 'tuk') {
                    return $jadwal->tuk->{$parts[2]} ?? '';
                }
                return $jadwal->{$field} ?? '';

            case 'system':
                if ($field === 'tanggal_generate') {
                    return now()->format('d-m-Y');
                } elseif ($field === 'waktu_generate') {
                    return now()->format('H:i:s');
                }
                return '';

            default:
                return '';
        }
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

        // Get all assessed asesi for this jadwal using AsesiPenilaian (sistem baru)
        $asesiPenilaianList = AsesiPenilaian::with('asesi')
            ->where('jadwal_id', $jadwalId)
            ->where('hasil_akhir', '!=', 'belum_dinilai')
            ->get();

        // Check if all asesi have been assessed
        $totalAsesi = PendaftaranUjikom::where('jadwal_id', $jadwalId)->count();
        $assessedAsesi = $asesiPenilaianList->count();

        if ($totalAsesi != $assessedAsesi) {
            return redirect()->back()->with('error', 'Belum semua asesi dinilai. Silakan nilai semua asesi terlebih dahulu.');
        }

        // Map ke format yang sama untuk kompatibilitas view
        $asesiList = $asesiPenilaianList->map(function($penilaian) {
            return (object)[
                'asesi' => $penilaian->asesi,
                'status' => $penilaian->hasil_akhir === 'kompeten' ? 5 : 4,
                'id' => $penilaian->id,
            ];
        });

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
                    'nim' => $item->asesi->nim ?? '-',
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

            // Get all assessed asesi for this jadwal using AsesiPenilaian (sistem baru)
            $asesiPenilaianList = AsesiPenilaian::with('asesi')
                ->where('jadwal_id', $jadwalId)
                ->where('hasil_akhir', '!=', 'belum_dinilai')
                ->get();

            // Map ke format yang kompatibel untuk processing
            $asesiList = $asesiPenilaianList->map(function($penilaian) {
                return (object)[
                    'asesi' => $penilaian->asesi,
                    'status' => $penilaian->hasil_akhir === 'kompeten' ? 5 : 4,
                    'id' => $penilaian->id,
                ];
            });

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

            // Get asesor and skema data
            $asesor = Auth::user();
            $skema = $jadwal->skema;
            $tuk = $jadwal->tuk;

            // Prepare comprehensive auto-filled data
            $autoFilledData = [
                // Legacy placeholders (backward compatibility)
                'skema.judul' => $skema->nama,
                'skema.nomor' => $skema->kode ?? '',
                'tuk' => $tuk->nama,
                'nama_asesor' => $asesor->name,
                'tanggal' => now()->format('d-m-Y'),
                'asesi_kompeten' => $kompeten,
                'asesi_tidak_kompeten' => $tidakKompeten,
                'total_asesi' => $asesiList->count(),

                // Skema fields (standard database fields)
                'skema.nama' => $skema->nama,
                'skema.kode' => $skema->kode ?? '',
                'skema.kategori' => $skema->kategori ?? '',
                'skema.bidang' => $skema->bidang ?? '',

                // Asesor fields (standard database fields)
                'asesor.name' => $asesor->name,
                'asesor.email' => $asesor->email ?? '',
                'asesor.telephone' => $asesor->telephone ?? '',
                'asesor.nik' => $asesor->nik ?? '',
                'asesor.nip' => $asesor->nip ?? '',
                'asesor.tempat_lahir' => $asesor->tempat_lahir ?? '',
                'asesor.tanggal_lahir' => $asesor->tanggal_lahir ? ($asesor->tanggal_lahir instanceof \DateTime ? $asesor->tanggal_lahir->format('d/m/Y') : $asesor->tanggal_lahir) : '',
                'asesor.jenis_kelamin' => $asesor->jenis_kelamin ?? '',
                'asesor.alamat' => $asesor->alamat ?? '',
                'asesor.pendidikan' => $asesor->pendidikan ?? '',

                // Jadwal fields
                'jadwal.tanggal_ujian' => $jadwal->tanggal_ujian,
                'jadwal.waktu_mulai' => $jadwal->waktu_mulai ?? '',
                'jadwal.waktu_selesai' => $jadwal->waktu_selesai ?? '',
                'jadwal.tuk.nama' => $tuk->nama ?? '',
                'jadwal.tuk.alamat' => $tuk->alamat ?? '',

                // System fields
                'system.tanggal_generate' => now()->format('d-m-Y'),
                'system.waktu_generate' => now()->format('H:i:s'),
            ];

            // Process custom field mappings from template (if any)
            if ($template->field_mappings && is_array($template->field_mappings)) {
                foreach ($template->field_mappings as $placeholderName => $databaseField) {
                    // Get the value from database using the field path
                    $value = $this->getFieldValueFromPath($databaseField, $jadwal, $asesor, $skema);

                    if ($value !== null) {
                        $autoFilledData[$placeholderName] = $value;
                    }
                }
            }

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
