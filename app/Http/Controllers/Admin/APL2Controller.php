<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\MenuTrait;
use App\Models\Skema;
use App\Models\APL2;
use App\Models\TemplateMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class APL2Controller extends Controller
{
    use MenuTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lists = $this->getMenuListAdmin('apl-2');
        $skema = Skema::with('apl2')->orderBy('nama', 'asc')->get();
        return view('components.pages.admin.apl2.list', compact('lists', 'skema'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $skema_id)
    {
        $lists = $this->getMenuListAdmin('apl-2');
        $skema = Skema::find($skema_id);

        // Available fields untuk template APL2
        $availableFields = [
            'user.name' => 'Nama Asesi',
            'user.email' => 'Email Asesi',
            'user.telephone' => 'Telepon Asesi',
            'user.alamat' => 'Alamat Asesi',
            'user.nik' => 'NIK Asesi',
            'user.nim' => 'NIM Asesi',
            'skema.nama' => 'Nama Skema',
            'skema.kode' => 'Kode Skema',
            'skema.bidang' => 'Bidang Skema',
            'jadwal.tanggal_ujian' => 'Tanggal Ujian',
            'jadwal.waktu_mulai' => 'Waktu Mulai',
            'jadwal.tuk.nama' => 'Lokasi Ujian',
        ];

        return view('components.pages.admin.apl2.create', compact('lists', 'skema', 'availableFields'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'skema_id' => 'required',
            'variables' => 'required|string',
            'custom_variables' => 'nullable|array',
            'custom_variables.*.name' => 'required_with:custom_variables|string',
            'custom_variables.*.label' => 'required_with:custom_variables|string',
            'custom_variables.*.type' => 'required_with:custom_variables|string',
            'custom_variables.*.options' => 'nullable|string',
            'custom_variables.*.required' => 'nullable|boolean',
        ]);

        try {
            // Parse variables dari JSON
            $variables = json_decode($request->variables, true);

            // Process custom variables
            $customVariables = [];
            if ($request->has('custom_variables')) {
                foreach ($request->custom_variables as $customVar) {
                    if (!empty($customVar['name']) && !empty($customVar['label'])) {
                        $customVarData = [
                            'name' => $customVar['name'],
                            'label' => $customVar['label'],
                            'type' => $customVar['type'],
                            'required' => $customVar['required'] ?? false,
                        ];

                        // Add options if provided
                        if (!empty($customVar['options']) && in_array($customVar['type'], ['checkbox', 'radio', 'select'])) {
                            $customVarData['options'] = array_map('trim', explode(',', $customVar['options']));
                        }

                        $customVariables[] = $customVarData;
                    }
                }
            }

            // Simpan ke TemplateMaster sebagai template APL2
            TemplateMaster::create([
                'nama_template' => 'APL2 - ' . Skema::find($request->skema_id)->nama,
                'tipe_template' => 'APL2',
                'skema_id' => $request->skema_id,
                'deskripsi' => 'Template APL2 dengan custom variables',
                'variables' => json_encode($variables),
                'custom_variables' => json_encode($customVariables),
                'file_template' => null, // File template akan diupload terpisah
                'ttd_digital' => null,
            ]);

            return redirect()->route('admin.apl-2.show-by-skema', $request->skema_id)->with('success', 'Template APL2 dengan custom variables berhasil dibuat');
        } catch (\Exception $e) {
            return redirect()->route('admin.apl-2.create', $request->skema_id)->withInput()->with('error', 'Template APL2 gagal dibuat: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lists = $this->getMenuListAdmin('apl-2');
        $apl2 = APL2::findOrFail($id);
        $skema = $apl2->skema;
        return view('components.pages.admin.apl2.detail', compact('lists', 'apl2', 'skema'));
    }

    /**
     * Display questions by skema
     */
    public function showBySkema(string $skema_id)
    {
        $lists = $this->getMenuListAdmin('apl-2');
        $skema = Skema::findOrFail($skema_id);
        $questions = APL2::where('skema_id', $skema_id)->get();
        return view('components.pages.admin.apl2.show', compact('lists', 'questions', 'skema'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lists = $this->getMenuListAdmin('apl-2');
        $apl2 = APL2::find($id);
        $skema = Skema::find($apl2->skema_id);
        return view('components.pages.admin.apl2.edit', compact('lists', 'apl2', 'skema'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'skema_id' => 'required',
            'question_text' => 'required',
            'question_type' => 'required|in:text,textarea,checkbox,radio,select,file',
            'question_options' => 'nullable|string',
        ]);

        try {
            $apl2 = APL2::findOrFail($id);

            // Process question options
            $questionOptions = null;
            if ($request->question_options) {
                // Split by comma and trim whitespace
                $options = array_map('trim', explode(',', $request->question_options));
                $questionOptions = $options;
            }

            $apl2->update([
                'skema_id' => $request->skema_id,
                'question_text' => $request->question_text,
                'question_type' => $request->question_type,
                'question_options' => $questionOptions,
            ]);

            return redirect()->route('admin.apl-2.show-by-skema', $request->skema_id)->with('success', 'APL02 berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->route('admin.apl-2.show-by-skema', $request->skema_id)->withInput()->with('error', 'APL02 gagal diperbarui: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $apl2 = APL2::findOrFail($id);
        try {
            $apl2->delete();

            return redirect()->route('admin.apl-2.show-by-skema', $apl2->skema_id)->with('success', 'APL02 berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.apl-2.show-by-skema', $apl2->skema_id)->with('error', 'APL02 gagal dihapus');
        }
    }

    /**
     * Display APL2 template management
     */
    public function templateIndex()
    {
        $lists = $this->getMenuListAdmin('apl-2-template');
        $templates = TemplateMaster::where('tipe_template', 'APL2')
            ->with('skema')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('components.pages.admin.apl2.template.index', compact('lists', 'templates'));
    }

    /**
     * Show form to create APL2 template
     */
    public function templateCreate()
    {
        $lists = $this->getMenuListAdmin('apl-2-template');
        $skemas = Skema::orderBy('nama', 'asc')->get();

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

        return view('components.pages.admin.apl2.template.create', compact('lists', 'skemas', 'availableFields'));
    }

    /**
     * Show the form for editing APL2 template
     */
    public function templateEdit($id)
    {
        $lists = $this->getMenuListAdmin('apl-2');
        $template = TemplateMaster::findOrFail($id);

        if ($template->tipe_template !== 'APL2') {
            return redirect()->back()->with('error', 'Template bukan APL2.');
        }

        $skema = $template->skema;
        $allSkema = Skema::orderBy('nama', 'asc')->get();

        // Available fields untuk template APL2
        $availableFields = [
            'user.name' => 'Nama Asesi',
            'user.email' => 'Email Asesi',
            'user.telephone' => 'Telepon Asesi',
            'user.alamat' => 'Alamat Asesi',
            'user.nik' => 'NIK Asesi',
            'user.nim' => 'NIM Asesi',
            'skema.nama' => 'Nama Skema',
            'skema.kode' => 'Kode Skema',
            'skema.bidang' => 'Bidang Skema',
            'jadwal.tanggal_ujian' => 'Tanggal Ujian',
            'jadwal.waktu_mulai' => 'Waktu Mulai',
            'jadwal.tuk.nama' => 'Lokasi Ujian',
        ];

        return view('components.pages.admin.apl2.template.edit', compact('lists', 'template', 'skema', 'allSkema', 'availableFields'));
    }

    /**
     * Update APL2 template
     */
    public function templateUpdate(Request $request, $id)
    {
        $template = TemplateMaster::findOrFail($id);

        if ($template->tipe_template !== 'APL2') {
            return redirect()->back()->with('error', 'Template bukan APL2.');
        }

        $request->validate([
            'nama_template' => 'required|string|max:255',
            'skema_id' => 'required|exists:skema,id',
            'deskripsi' => 'nullable|string',
            'file_template' => 'nullable|file|mimes:docx|max:10240',
            'ttd_digital' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
            'custom_variables.*.name' => 'required|string|max:255',
            'custom_variables.*.label' => 'nullable|string|max:255',
            'custom_variables.*.type' => 'nullable|string|in:text,textarea,checkbox,radio,select,number,email,date,file',
            'custom_variables.*.options' => 'nullable|string',
            'custom_variables.*.required' => 'nullable|boolean',
        ]);

        try {
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

            // Update template
            $template->update([
                'nama_template' => $request->nama_template,
                'skema_id' => $request->skema_id,
                'deskripsi' => $request->deskripsi,
                'file_path' => $templatePath,
                'ttd_path' => $ttdPath,
                'custom_variables' => $customVariables,
            ]);

            return redirect()->route('admin.apl-2.template.index')->with('success', 'Template APL2 berhasil diupdate!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Store APL2 template
     */
    public function templateStore(Request $request)
    {
        $request->validate([
            'nama_template' => 'required|string|max:255',
            'skema_id' => 'required|exists:skema,id',
            'deskripsi' => 'nullable|string',
            'file_template' => 'required|file|mimes:docx|max:10240',
            'ttd_digital' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
            'variables' => 'required|string',
            'custom_variables' => 'nullable|array',
            'custom_variables.*.name' => 'nullable|string|max:255',
            'custom_variables.*.label' => 'nullable|string|max:255',
            'custom_variables.*.type' => 'nullable|string|in:text,textarea,checkbox,radio,select,number,email,date,file',
            'custom_variables.*.options' => 'nullable|string',
            'custom_variables.*.required' => 'nullable|boolean',
        ]);

        try {
            // Parse variables dari JSON
            $variables = json_decode($request->variables, true);
            if (!$variables || !is_array($variables)) {
                return redirect()->back()->with('error', 'Variable tidak valid.')->withInput();
            }

            // Upload template file
            $templateFile = $request->file('file_template');
            $templateFileName = 'apl2_template_' . time() . '_' . Str::slug($request->nama_template) . '.docx';
            $templatePath = $templateFile->storeAs('templates/apl2', $templateFileName, 'public');

            // Upload TTD file jika ada
            $ttdPath = null;
            if ($request->hasFile('ttd_digital')) {
                $ttdFile = $request->file('ttd_digital');
                $ttdFileName = 'apl2_ttd_' . time() . '_' . Str::slug($request->nama_template) . '.' . $ttdFile->getClientOriginalExtension();
                $ttdPath = $ttdFile->storeAs('ttd/apl2', $ttdFileName, 'public');
            }

            // Cek apakah sudah ada template dengan skema yang sama
            $existingTemplate = TemplateMaster::where('tipe_template', 'APL2')
                ->where('skema_id', $request->skema_id)
                ->first();

            if ($existingTemplate) {
                return redirect()->back()->withInput()->with('error', 'Template APL2 untuk skema ini sudah ada. Silakan edit template yang sudah ada atau pilih skema yang berbeda.');
            }

            // Parse field configurations untuk semua tipe template
            $fieldConfigurations = null;
            $fieldMappings = null;

            // Handle custom variables
            $customVariables = [];
            if ($request->custom_variables) {
                foreach ($request->custom_variables as $customVar) {
                    if (!empty($customVar['name']) && !empty($customVar['label'])) {
                        $customVariables[] = $customVar;
                    }
                }
            }

            // Buat template master
            TemplateMaster::create([
                'nama_template' => $request->nama_template,
                'tipe_template' => 'APL2',
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

            return redirect()->route('admin.apl-2.template.index')->with('success', 'Template APL2 berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
