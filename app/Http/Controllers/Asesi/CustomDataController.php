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
        $pendaftaran = Pendaftaran::where('id', $pendaftaranId)
            ->where('user_id', Auth::id())
            ->where('status', 4) // Menunggu Ujian
            ->with(['skema'])
            ->first();

        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Pendaftaran tidak ditemukan.');
        }

        // Get template untuk melihat custom variables yang bisa diisi
        $template = \App\Models\TemplateMaster::active()
            ->byType('APL1')
            ->where('skema_id', $pendaftaran->skema_id)
            ->first();

        if (!$template) {
            return redirect()->back()->with('error', 'Template untuk skema ini belum tersedia.');
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
        if ($template->variables) {
            foreach ($template->variables as $variable) {
                if (!in_array($variable, $databaseFields)) {
                    $customVariables[] = $variable;
                }
            }
        }

        $lists = $this->getMenuListAsesi('sertifikasi');
        $activeMenu = 'sertifikasi';

        return view('components.pages.asesi.custom-data.form', compact('pendaftaran', 'template', 'customVariables', 'lists', 'activeMenu'));
    }

    /**
     * Store custom variables dan TTD
     */
    public function store(Request $request, $pendaftaranId)
    {
        $request->validate([
            'custom_variables' => 'nullable|array',
            'custom_variables.*' => 'nullable|string|max:255',
            'ttd_asesi' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
        ]);

        $pendaftaran = Pendaftaran::where('id', $pendaftaranId)
            ->where('user_id', Auth::id())
            ->where('status', 4)
            ->first();

        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Pendaftaran tidak ditemukan.');
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

            $ttdAsesiPath = null;
            if ($request->hasFile('ttd_asesi')) {
                // Hapus TTD lama jika ada
                if ($pendaftaran->ttd_asesi_path && Storage::disk('public')->exists($pendaftaran->ttd_asesi_path)) {
                    Storage::disk('public')->delete($pendaftaran->ttd_asesi_path);
                }

                // Upload TTD baru
                $ttdFile = $request->file('ttd_asesi');
                $ttdFileName = 'ttd_asesi_' . $pendaftaranId . '_' . time() . '.' . $ttdFile->getClientOriginalExtension();
                $ttdAsesiPath = $ttdFile->storeAs('ttd_asesi', $ttdFileName, 'public');
            }

            // Update pendaftaran
            $pendaftaran->update([
                'custom_variables' => $customVariables,
                'ttd_asesi_path' => $ttdAsesiPath ?: $pendaftaran->ttd_asesi_path,
            ]);

            return redirect()->route('asesi.sertifikasi.index')
                ->with('success', 'Data custom berhasil disimpan!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
