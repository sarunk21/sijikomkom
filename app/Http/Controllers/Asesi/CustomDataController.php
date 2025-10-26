<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CustomDataController extends Controller
{
    use MenuTrait;
    /**
     * Show form untuk input custom variables dan TTD
     */
    public function showForm($pendaftaranId)
    {
        try {
        $pendaftaran = Pendaftaran::where('id', $pendaftaranId)
            ->where('user_id', Auth::id())
            ->with(['skema', 'user'])
            ->first();

            if (!$pendaftaran) {
                return redirect()->back()->with('error', 'Pendaftaran tidak ditemukan atau bukan milik Anda.');
            }

            // Cek status pendaftaran - sekarang lebih fleksibel
            if (!in_array($pendaftaran->status, [3, 4, 5])) {
                return redirect()->back()->with('error', 'Pendaftaran belum dalam status yang tepat untuk input custom data. Status saat ini: ' . $pendaftaran->status_text);
            }

            // Get template untuk melihat custom variables yang bisa diisi
            $template = \App\Models\TemplateMaster::active()
                ->byType('APL1')
                ->where('skema_id', $pendaftaran->skema_id)
                ->first();

            if (!$template) {
                return redirect()->back()->with('error', 'Template APL1 untuk skema "' . $pendaftaran->skema->nama . '" belum tersedia. Silakan hubungi administrator.');
            }

        // Filter custom variables (yang tidak ada di database fields)
        $databaseFields = [
            'user.name', 'user.email', 'user.telephone', 'user.alamat', 'user.nik', 'user.nim',
            'user.tempat_lahir', 'user.tanggal_lahir', 'user.jenis_kelamin', 'user.kebangsaan',
            'user.pekerjaan', 'user.pendidikan', 'user.jurusan',
            'skema.nama', 'skema.kode', 'skema.kategori', 'skema.bidang',
            'jadwal.tanggal_ujian', 'jadwal.waktu_mulai', 'jadwal.waktu_selesai', 'jadwal.tuk.nama',
            'system.tanggal_generate', 'system.waktu_generate', 'system.nomor_pendaftaran',
            'ttd_digital'
        ];

        $customVariables = [];
        $existingData = [];
        $dynamicFields = [];

        if ($template->variables) {
            foreach ($template->variables as $variable) {
                if (!in_array($variable, $databaseFields)) {
                    // Cek apakah data sudah ada di profil user
                    $userData = $this->getUserDataForVariable($variable, $pendaftaran);
                    if ($userData) {
                        $existingData[$variable] = $userData;
                    } else {
                        $customVariables[] = $variable;
                    }
                }
            }
        }

        // Handle dynamic field configurations
        if ($template->field_configurations) {
            foreach ($template->field_configurations as $fieldConfig) {
                $fieldName = $fieldConfig['name'];
                $fieldMapping = $template->field_mappings[$fieldName] ?? '';

                // Cek apakah field sudah ada di database
                if ($fieldMapping) {
                    $userData = $this->getUserDataForVariable($fieldMapping, $pendaftaran);
                    if ($userData) {
                        $existingData[$fieldName] = $userData;
                    } else {
                        $dynamicFields[] = $fieldConfig;
                    }
                } else {
                    // Custom field, perlu diisi
                    $dynamicFields[] = $fieldConfig;
                }
            }
        }

        // Handle custom variables dari template (legacy)
        if ($template->custom_variables) {
            foreach ($template->custom_variables as $customVar) {
                $fieldName = $customVar['name'];
                $fieldMapping = $customVar['database_mapping'] ?? '';

                // Cek apakah field sudah ada di database
                if ($fieldMapping) {
                    $userData = $this->getUserDataForVariable($fieldMapping, $pendaftaran);
                    if ($userData) {
                        $existingData[$fieldName] = $userData;
                    } else {
                        $dynamicFields[] = $customVar;
                    }
                } else {
                    // Custom field, perlu diisi
                    $dynamicFields[] = $customVar;
                }
            }
        }

            $lists = $this->getMenuListAsesi('sertifikasi');
            $activeMenu = 'sertifikasi';

            return view('components.pages.asesi.custom-data.form', compact('pendaftaran', 'template', 'customVariables', 'existingData', 'dynamicFields', 'lists', 'activeMenu'));

        } catch (\Exception $e) {
            \Log::error('CustomDataController showForm error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get user data for specific variable
     */
    private function getUserDataForVariable($variable, $pendaftaran)
    {
        try {
            $user = $pendaftaran->user;

            if (!$user) {
                return null;
            }

            switch ($variable) {
            case 'nama_lengkap':
                return $user->name;
            case 'email_pribadi':
                return $user->email;
            case 'no_hp':
                return $user->telephone;
            case 'alamat':
                return $user->alamat;
            case 'nik':
                return $user->nik;
            case 'nim':
                return $user->nim;
            case 'tempat_lahir':
                return $user->tempat_lahir;
            case 'tanggal_lahir':
                if ($user->tanggal_lahir) {
                    // Cek apakah sudah berupa string atau Carbon instance
                    if (is_string($user->tanggal_lahir)) {
                        return $user->tanggal_lahir;
                    }
                    return $user->tanggal_lahir->format('d/m/Y');
                }
                return null;
            case 'jenis_kelamin':
                return $user->jenis_kelamin;
            case 'kebangsaan':
                return $user->kebangsaan;
            case 'pekerjaan':
                return $user->pekerjaan;
            case 'pendidikan':
                return $user->pendidikan;
            case 'jurusan':
                return $user->jurusan;
            default:
                return null;
            }
        } catch (\Exception $e) {
            \Log::error('Error getting user data for variable ' . $variable . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Store custom variables dan TTD
     */
    public function store(Request $request, $pendaftaranId)
    {
        $request->validate([
            'custom_variables' => 'nullable|array',
            'custom_variables.*' => 'nullable|string|max:255',
            'signature_data' => 'nullable|string',
            'dynamic_fields' => 'nullable|array',
            'dynamic_fields.*' => 'nullable',
        ]);

        $pendaftaran = Pendaftaran::where('id', $pendaftaranId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Pendaftaran tidak ditemukan atau bukan milik Anda.');
        }

        // Cek status pendaftaran - sekarang lebih fleksibel
        if (!in_array($pendaftaran->status, [3, 4, 5])) {
            return redirect()->back()->with('error', 'Pendaftaran belum dalam status yang tepat untuk input custom data. Status saat ini: ' . $pendaftaran->status_text);
        }

        try {
            $customVariables = [];
            if ($request->custom_variables) {
                // Filter out empty values
                foreach ($request->custom_variables as $key => $value) {
                    if (!empty(trim($value))) {
                        $customVariables[$key] = trim($value);
                    }
                }
            }

            // Merge dynamic fields
            if ($request->dynamic_fields) {
                foreach ($request->dynamic_fields as $key => $value) {
                    if (is_array($value)) {
                        $customVariables[$key] = implode(', ', $value);
                    } else {
                        $customVariables[$key] = $value;
                    }
                }
            }

            $ttdAsesiPath = null;
            if ($request->signature_data) {
                // Hapus TTD lama jika ada
                if ($pendaftaran->ttd_asesi_path && Storage::disk('public')->exists($pendaftaran->ttd_asesi_path)) {
                    Storage::disk('public')->delete($pendaftaran->ttd_asesi_path);
                }

                // Simpan signature digital sebagai file PNG
                $signatureData = $request->signature_data;
                $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signatureData));

                $ttdFileName = 'ttd_asesi_' . $pendaftaranId . '_' . time() . '.png';
                $ttdAsesiPath = 'ttd_asesi/' . $ttdFileName;

                Storage::disk('public')->put($ttdAsesiPath, $image);
            }

            // Update pendaftaran
            $pendaftaran->update([
                'custom_variables' => $customVariables,
                'ttd_asesi_path' => $ttdAsesiPath ?: $pendaftaran->ttd_asesi_path,
            ]);

            return redirect()->route('asesi.sertifikasi.index')
                ->with('success', 'Data berhasil disimpan!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
