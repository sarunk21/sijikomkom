<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateMaster;
use App\Models\Skema;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminTemplateController extends Controller
{
    use MenuTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lists = $this->getMenuListAdmin('template-master');
        $activeMenu = 'template-master';

        $templates = TemplateMaster::with('skema')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('components.pages.admin.template-master.list', compact('lists', 'activeMenu', 'templates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lists = $this->getMenuListAdmin('template-master');
        $activeMenu = 'template-master';

        $skemas = Skema::orderBy('nama', 'asc')->get();

        $tipeTemplateOptions = [
            'APL1' => 'APL 1 (Asesmen Mandiri)',
            'APL2' => 'APL 2 (Portofolio)',
            'FR_AK_05' => 'FR AK 05 (Form Asesmen Asesor)',
        ];

        // Field database yang tersedia untuk variable
        $availableFields = [
            // User fields - Data Identitas
            'user.name' => 'Nama Lengkap',
            'user.email' => 'Email',
            'user.telephone' => 'Nomor Telepon',
            'user.nik' => 'NIK',
            'user.nim' => 'NIM',

            // User fields - Data Kelahiran
            'user.tempat_lahir' => 'Tempat Lahir',
            'user.tanggal_lahir' => 'Tanggal Lahir',
            'user.jenis_kelamin' => 'Jenis Kelamin',

            // User fields - Alamat & Kewarganegaraan
            'user.alamat' => 'Alamat Lengkap',
            'user.kebangsaan' => 'Kebangsaan',

            // User fields - Pekerjaan & Pendidikan
            'user.pekerjaan' => 'Pekerjaan',
            'user.pendidikan' => 'Pendidikan Terakhir',
            'user.jurusan' => 'Jurusan',

            // User fields - Foto & Dokumen (Path)
            'user.photo_diri' => 'Foto Diri (Path)',
            'user.photo_ktp' => 'Foto KTP (Path)',
            'user.photo_sertifikat' => 'Foto Sertifikat (Path)',
            'user.photo_ktmkhs' => 'Foto KTM/KHS (Path)',
            'user.photo_administatif' => 'Foto Administratif (Path)',
            'user.tanda_tangan' => 'Tanda Tangan Digital (Path)',

            // Asesor fields - Data Identitas
            'asesor.name' => 'Nama Asesor',
            'asesor.email' => 'Email Asesor',
            'asesor.telephone' => 'Nomor Telepon Asesor',
            'asesor.nik' => 'NIK Asesor',
            'asesor.nip' => 'NIP Asesor',

            // Asesor fields - Data Lainnya
            'asesor.tempat_lahir' => 'Tempat Lahir Asesor',
            'asesor.tanggal_lahir' => 'Tanggal Lahir Asesor',
            'asesor.jenis_kelamin' => 'Jenis Kelamin Asesor',
            'asesor.alamat' => 'Alamat Asesor',
            'asesor.pendidikan' => 'Pendidikan Asesor',
            'asesor.tanda_tangan' => 'Tanda Tangan Asesor (Path)',

            // Skema fields
            'skema.nama' => 'Nama Skema',
            'skema.kode' => 'Kode Skema',
            'skema.kategori' => 'Kategori Skema',
            'skema.bidang' => 'Bidang Skema',

            // Jadwal fields
            'jadwal.tanggal_ujian' => 'Tanggal Ujian',
            'jadwal.tanggal_selesai' => 'Tanggal Selesai Ujian',
            'jadwal.tanggal_maksimal_pendaftaran' => 'Batas Akhir Pendaftaran',
            'jadwal.waktu_mulai' => 'Waktu Mulai',
            'jadwal.waktu_selesai' => 'Waktu Selesai',
            'jadwal.kuota' => 'Kuota Peserta',
            'jadwal.tuk.nama' => 'Lokasi Ujian (TUK)',
            'jadwal.tuk.kode' => 'Kode TUK',
            'jadwal.tuk.alamat' => 'Alamat TUK',

            // System fields
            'system.tanggal_generate' => 'Tanggal Generate Dokumen',
            'system.waktu_generate' => 'Waktu Generate Dokumen',
            'system.nomor_pendaftaran' => 'Nomor Pendaftaran',
            'system.tahun' => 'Tahun',
            'system.bulan' => 'Bulan',
        ];

        return view('components.pages.admin.template-master.create', compact('lists', 'activeMenu', 'skemas', 'tipeTemplateOptions', 'availableFields'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_template' => 'nullable|string|max:255',
            'tipe_template' => 'required|string|in:APL1,APL2,FR_AK_05',
            'skema_id' => 'required|exists:skema,id',
            'deskripsi' => 'nullable|string',
            'file_template' => 'required|file|mimes:docx|max:10240', // Max 10MB
            'ttd_digital' => 'nullable|file|mimes:png,jpg,jpeg|max:2048', // Max 2MB
            'variables' => 'required|string', // JSON string dari JavaScript
            'custom_variables' => 'nullable|array',
            'custom_variables.*.name' => 'nullable|string|max:255',
            'custom_variables.*.label' => 'nullable|string|max:255',
            'custom_variables.*.type' => 'nullable|string|in:text,textarea,checkbox,radio,select,number,email,date,file,signature_pad',
            'custom_variables.*.options' => 'nullable|string',
            'custom_variables.*.required' => 'nullable|boolean',
            'custom_variables.*.role' => 'nullable|string|in:asesi,asesor,both',
            // Dynamic field configurations
            'field_configurations' => 'nullable|string',
            'field_mappings' => 'nullable|string',
        ], [
            'tipe_template.required' => 'Tipe Template wajib dipilih.',
            'tipe_template.in' => 'Tipe Template yang dipilih tidak valid.',
            'skema_id.required' => 'Skema wajib dipilih.',
            'skema_id.exists' => 'Skema yang dipilih tidak ditemukan.',
            'file_template.required' => 'File Template wajib diupload.',
            'file_template.file' => 'File yang diupload tidak valid.',
            'file_template.mimes' => 'File Template harus berformat .docx.',
            'file_template.max' => 'Ukuran file Template maksimal 10MB.',
            'ttd_digital.file' => 'File TTD Digital yang diupload tidak valid.',
            'ttd_digital.mimes' => 'File TTD Digital harus berformat PNG, JPG, atau JPEG.',
            'ttd_digital.max' => 'Ukuran file TTD Digital maksimal 2MB.',
            'variables.required' => 'Variables wajib diisi.',
            'variables.string' => 'Variables harus berupa string.',
        ]);

        // Validasi: Tipe Template dan Skema hanya ada 1
        $existingTemplate = TemplateMaster::where('tipe_template', $request->tipe_template)
            ->where('skema_id', $request->skema_id)
            ->first();

        if ($existingTemplate) {
            return redirect()->back()->withInput()->with('error', 'Template dengan kombinasi Tipe Template dan Skema yang sama sudah ada. Silakan edit template yang sudah ada atau pilih kombinasi yang berbeda.');
        }

        try {
            // Parse variables dari JSON
            $variables = json_decode($request->variables, true);
            if (!$variables || !is_array($variables)) {
                return redirect()->back()->with('error', 'Variable tidak valid.')->withInput();
            }

            // Upload template file
            $templateFile = $request->file('file_template');
            $templateFileName = 'template_' . time() . '_' . Str::slug($request->nama_template) . '.docx';
            $templatePath = $templateFile->storeAs('templates', $templateFileName, 'public');

            // Upload TTD file jika ada
            $ttdPath = null;
            if ($request->hasFile('ttd_digital')) {
                $ttdFile = $request->file('ttd_digital');
                $ttdFileName = 'ttd_' . time() . '_' . Str::slug($request->nama_template) . '.' . $ttdFile->getClientOriginalExtension();
                $ttdPath = $ttdFile->storeAs('ttd', $ttdFileName, 'public');
            }

            // Parse APL2 config jika ada
            $apl2Config = null;
            $apl2Questions = null;
            $apl2CheckboxConfig = null;
            $fieldConfigurations = null;
            $fieldMappings = null;

            // Parse field configurations untuk semua tipe template
            if ($request->field_configurations) {
                $fieldConfigurations = json_decode($request->field_configurations, true);
            }

            if ($request->field_mappings) {
                $fieldMappings = json_decode($request->field_mappings, true);
            }

            // Handle custom variables
            $customVariables = [];
            if ($request->custom_variables) {
                foreach ($request->custom_variables as $customVar) {
                    if (!empty($customVar['name']) && !empty($customVar['label'])) {
                        $customVariables[] = $customVar;
                    }
                }
            }

            // Generate nama_template default jika tidak ada
            $skema = Skema::find($request->skema_id);
            $tipeLabels = [
                'APL1' => 'APL 1 (Asesmen Mandiri)',
                'APL2' => 'APL 2 (Portofolio)',
                'FR_AK_05' => 'FR AK 05 (Form Asesmen Asesor)',
            ];
            $tipeLabel = $tipeLabels[$request->tipe_template] ?? $request->tipe_template;
            $namaTemplate = $request->nama_template ?? ($skema ? $skema->nama . ' - ' . $tipeLabel : $tipeLabel);

            // Buat template master
            $template = TemplateMaster::create([
                'nama_template' => $namaTemplate,
                'tipe_template' => $request->tipe_template,
                'skema_id' => $request->skema_id,
                'deskripsi' => $request->deskripsi,
                'file_path' => $templatePath,
                'ttd_path' => $ttdPath,
                'variables' => $request->variables,
                'is_active' => true,
                'field_configurations' => $fieldConfigurations,
                'field_mappings' => $fieldMappings,
                'custom_variables' => $customVariables,
            ]);

            return redirect()->route('admin.template-master.index')->with('success', 'Template berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $template = TemplateMaster::with('skema')->findOrFail($id);
        $lists = $this->getMenuListAdmin('template-master');
        $activeMenu = 'template-master';

        return view('components.pages.admin.template-master.show', compact('lists', 'activeMenu', 'template'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $template = TemplateMaster::with('skema')->findOrFail($id);
        $lists = $this->getMenuListAdmin('template-master');
        $activeMenu = 'template-master';

        $skemas = Skema::orderBy('nama', 'asc')->get();

        $tipeTemplateOptions = [
            'APL1' => 'APL 1 (Asesmen Mandiri)',
            'APL2' => 'APL 2 (Portofolio)',
            'FR_AK_05' => 'FR AK 05 (Form Asesmen Asesor)',
        ];

        // Field database yang tersedia untuk variable
        $availableFields = [
            // User fields - Data Identitas
            'user.name' => 'Nama Lengkap',
            'user.email' => 'Email',
            'user.telephone' => 'Nomor Telepon',
            'user.nik' => 'NIK',
            'user.nim' => 'NIM',

            // User fields - Data Kelahiran
            'user.tempat_lahir' => 'Tempat Lahir',
            'user.tanggal_lahir' => 'Tanggal Lahir',
            'user.jenis_kelamin' => 'Jenis Kelamin',

            // User fields - Alamat & Kewarganegaraan
            'user.alamat' => 'Alamat Lengkap',
            'user.kebangsaan' => 'Kebangsaan',

            // User fields - Pekerjaan & Pendidikan
            'user.pekerjaan' => 'Pekerjaan',
            'user.pendidikan' => 'Pendidikan Terakhir',
            'user.jurusan' => 'Jurusan',

            // User fields - Foto & Dokumen (Path)
            'user.photo_diri' => 'Foto Diri (Path)',
            'user.photo_ktp' => 'Foto KTP (Path)',
            'user.photo_sertifikat' => 'Foto Sertifikat (Path)',
            'user.photo_ktmkhs' => 'Foto KTM/KHS (Path)',
            'user.photo_administatif' => 'Foto Administratif (Path)',
            'user.tanda_tangan' => 'Tanda Tangan Digital (Path)',

            // Asesor fields - Data Identitas
            'asesor.name' => 'Nama Asesor',
            'asesor.email' => 'Email Asesor',
            'asesor.telephone' => 'Nomor Telepon Asesor',
            'asesor.nik' => 'NIK Asesor',
            'asesor.nip' => 'NIP Asesor',

            // Asesor fields - Data Lainnya
            'asesor.tempat_lahir' => 'Tempat Lahir Asesor',
            'asesor.tanggal_lahir' => 'Tanggal Lahir Asesor',
            'asesor.jenis_kelamin' => 'Jenis Kelamin Asesor',
            'asesor.alamat' => 'Alamat Asesor',
            'asesor.pendidikan' => 'Pendidikan Asesor',
            'asesor.tanda_tangan' => 'Tanda Tangan Asesor (Path)',

            // Skema fields
            'skema.nama' => 'Nama Skema',
            'skema.kode' => 'Kode Skema',
            'skema.kategori' => 'Kategori Skema',
            'skema.bidang' => 'Bidang Skema',

            // Jadwal fields
            'jadwal.tanggal_ujian' => 'Tanggal Ujian',
            'jadwal.tanggal_selesai' => 'Tanggal Selesai Ujian',
            'jadwal.tanggal_maksimal_pendaftaran' => 'Batas Akhir Pendaftaran',
            'jadwal.waktu_mulai' => 'Waktu Mulai',
            'jadwal.waktu_selesai' => 'Waktu Selesai',
            'jadwal.kuota' => 'Kuota Peserta',
            'jadwal.tuk.nama' => 'Lokasi Ujian (TUK)',
            'jadwal.tuk.kode' => 'Kode TUK',
            'jadwal.tuk.alamat' => 'Alamat TUK',

            // System fields
            'system.tanggal_generate' => 'Tanggal Generate Dokumen',
            'system.waktu_generate' => 'Waktu Generate Dokumen',
            'system.nomor_pendaftaran' => 'Nomor Pendaftaran',
            'system.tahun' => 'Tahun',
            'system.bulan' => 'Bulan',
        ];

        return view('components.pages.admin.template-master.edit', compact('lists', 'activeMenu', 'template', 'skemas', 'tipeTemplateOptions', 'availableFields'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $template = TemplateMaster::findOrFail($id);

        $request->validate([
            'nama_template' => 'nullable|string|max:255',
            'tipe_template' => 'required|string|in:APL1,APL2,FR_AK_05',
            'skema_id' => 'required|exists:skema,id',
            'deskripsi' => 'nullable|string',
            'file_template' => 'nullable|file|mimes:docx|max:10240',
            'ttd_digital' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
            'variables' => 'required|string', // JSON string dari JavaScript
            'is_active' => 'boolean',
        ], [
            'tipe_template.required' => 'Tipe Template wajib dipilih.',
            'tipe_template.in' => 'Tipe Template yang dipilih tidak valid.',
            'skema_id.required' => 'Skema wajib dipilih.',
            'skema_id.exists' => 'Skema yang dipilih tidak ditemukan.',
            'file_template.file' => 'File Template yang diupload tidak valid.',
            'file_template.mimes' => 'File Template harus berformat .docx.',
            'file_template.max' => 'Ukuran file Template maksimal 10MB.',
            'ttd_digital.file' => 'File TTD Digital yang diupload tidak valid.',
            'ttd_digital.mimes' => 'File TTD Digital harus berformat PNG, JPG, atau JPEG.',
            'ttd_digital.max' => 'Ukuran file TTD Digital maksimal 2MB.',
            'variables.required' => 'Variables wajib diisi.',
            'variables.string' => 'Variables harus berupa string.',
        ]);

        // Validasi: Tipe Template dan Skema hanya ada 1
        $existingTemplate = TemplateMaster::where('tipe_template', $request->tipe_template)
            ->where('skema_id', $request->skema_id)
            ->where('id', '!=', $id)
            ->first();

        if ($existingTemplate) {
            return redirect()->back()->withInput()->with('error', 'Template dengan kombinasi Tipe Template dan Skema yang sama sudah ada. Silakan edit template yang sudah ada atau pilih kombinasi yang berbeda.');
        }

        try {
            // Parse variables dari JSON
            $variables = json_decode($request->variables, true);
            if (!$variables || !is_array($variables)) {
                return redirect()->back()->with('error', 'Variable tidak valid.')->withInput();
            }

            // Parse field configurations untuk semua tipe template
            $fieldConfigurations = null;
            $fieldMappings = null;
            if ($request->field_configurations) {
                $fieldConfigurations = json_decode($request->field_configurations, true);
            }
            if ($request->field_mappings) {
                $fieldMappings = json_decode($request->field_mappings, true);
            }

            // Handle custom variables
            $customVariables = [];
            if ($request->custom_variables) {
                foreach ($request->custom_variables as $customVar) {
                    if (!empty($customVar['name']) && !empty($customVar['label'])) {
                        $customVariables[] = $customVar;
                    }
                }
            }

            $templatePath = $template->file_path;
            $ttdPath = $template->ttd_path;

            // Upload template file baru jika ada
            if ($request->hasFile('file_template')) {
                // Hapus file lama
                if (Storage::disk('public')->exists($template->file_path)) {
                    Storage::disk('public')->delete($template->file_path);
                }

                $templateFile = $request->file('file_template');
                $templateFileName = 'template_' . time() . '_' . Str::slug($request->nama_template) . '.docx';
                $templatePath = $templateFile->storeAs('templates', $templateFileName, 'public');
            }

            // Upload TTD file baru jika ada
            if ($request->hasFile('ttd_digital')) {
                // Hapus file lama
                if ($template->ttd_path && Storage::disk('public')->exists($template->ttd_path)) {
                    Storage::disk('public')->delete($template->ttd_path);
                }

                $ttdFile = $request->file('ttd_digital');
                $ttdFileName = 'ttd_' . time() . '_' . Str::slug($request->nama_template) . '.' . $ttdFile->getClientOriginalExtension();
                $ttdPath = $ttdFile->storeAs('ttd', $ttdFileName, 'public');
            }

            // Generate nama_template default jika tidak ada
            $skema = Skema::find($request->skema_id);
            $tipeLabels = [
                'APL1' => 'APL 1 (Asesmen Mandiri)',
                'APL2' => 'APL 2 (Portofolio)',
                'FR_AK_05' => 'FR AK 05 (Form Asesmen Asesor)',
            ];
            $tipeLabel = $tipeLabels[$request->tipe_template] ?? $request->tipe_template;
            $namaTemplate = $request->nama_template ?? ($skema ? $skema->nama . ' - ' . $tipeLabel : $tipeLabel);

            // Update template
            $template->update([
                'nama_template' => $namaTemplate,
                'tipe_template' => $request->tipe_template,
                'skema_id' => $request->skema_id,
                'deskripsi' => $request->deskripsi,
                'file_path' => $templatePath,
                'ttd_path' => $ttdPath,
                'variables' => $variables,
                'is_active' => $request->has('is_active'),
                'field_configurations' => $fieldConfigurations,
                'field_mappings' => $fieldMappings,
                'custom_variables' => $customVariables,
            ]);

            // Redirect berdasarkan tipe template
            \Log::info('Template update redirect check', [
                'tipe_template' => $request->tipe_template,
                'template_id' => $template->id,
                'template_tipe' => $template->tipe_template
            ]);

            // Selalu redirect ke template master index untuk konsistensi
            return redirect()->route('admin.template-master.index')->with('success', 'Template berhasil diupdate!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $template = TemplateMaster::findOrFail($id);

            // Hapus file dari storage
            if (Storage::disk('public')->exists($template->file_path)) {
                Storage::disk('public')->delete($template->file_path);
            }

            if ($template->ttd_path && Storage::disk('public')->exists($template->ttd_path)) {
                Storage::disk('public')->delete($template->ttd_path);
            }

            if ($template->fr_ak_05_file_path && Storage::disk('public')->exists($template->fr_ak_05_file_path)) {
                Storage::disk('public')->delete($template->fr_ak_05_file_path);
            }

            // Hapus record dari database
            $template->delete();

            return redirect()->route('admin.template-master.index')->with('success', 'Template berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Download template file
     */
    public function download(string $id)
    {
        $template = TemplateMaster::findOrFail($id);
        $filePath = storage_path('app/public/' . $template->file_path);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File template tidak ditemukan.');
        }

        return response()->download($filePath, $template->nama_template . '.docx');
    }

    /**
     * Toggle status aktif/nonaktif
     */
    public function toggleStatus(string $id)
    {
        try {
            $template = TemplateMaster::findOrFail($id);
            $template->update(['is_active' => !$template->is_active]);

            $status = $template->is_active ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()->back()->with('success', "Template berhasil {$status}!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
