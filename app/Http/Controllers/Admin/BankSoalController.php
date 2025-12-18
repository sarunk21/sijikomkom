<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankSoal;
use App\Models\Skema;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BankSoalController extends Controller
{
    use MenuTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $lists = $this->getMenuListAdmin('bank-soal');
        $activeMenu = 'bank-soal';

        $query = BankSoal::with('skema');

        // Filter by skema
        if ($request->filled('skema_id')) {
            $query->where('skema_id', $request->skema_id);
        }

        // Filter by tipe
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $bankSoals = $query->orderBy('created_at', 'desc')->get();
        $skemas = Skema::orderBy('nama', 'asc')->get();

        return view('components.pages.admin.bank-soal.list', compact('lists', 'activeMenu', 'bankSoals', 'skemas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lists = $this->getMenuListAdmin('bank-soal');
        $activeMenu = 'bank-soal';

        $skemas = Skema::orderBy('nama', 'asc')->get();

        $tipeOptions = [
            'FR IA 03' => 'FR IA 03 - Formulir Asesmen Mandiri',
            'FR IA 06' => 'FR IA 06 - Formulir Asesmen Praktik',
            'FR IA 07' => 'FR IA 07 - Ceklis Observasi Asesor'
        ];

        $targetOptions = [
            'asesi' => 'Untuk Asesi',
            'asesor' => 'Untuk Asesor'
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
        ];

        return view('components.pages.admin.bank-soal.create', compact('lists', 'activeMenu', 'skemas', 'tipeOptions', 'targetOptions', 'availableFields'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'skema_id' => 'required|exists:skema,id',
            'nama' => 'nullable|string|max:255',
            'tipe' => 'required|in:FR IA 03,FR IA 06,FR IA 07',
            'target' => 'required|in:asesi,asesor',
        ], [
            'skema_id.required' => 'Skema Sertifikasi wajib dipilih.',
            'tipe.required' => 'Tipe Formulir wajib dipilih.',
            'target.required' => 'Target Pengguna wajib dipilih.',
        ]);

        // Validasi: Skema Sertifikasi, Tipe Formulir, dan Target Pengguna hanya ada 1
        $existing = BankSoal::where('skema_id', $request->skema_id)
            ->where('tipe', $request->tipe)
            ->where('target', $request->target)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Bank soal dengan kombinasi Skema Sertifikasi, Tipe Formulir, dan Target Pengguna yang sama sudah ada. Silakan edit bank soal yang sudah ada atau pilih kombinasi yang berbeda.');
        }

        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240', // max 10MB
            'keterangan' => 'nullable|string',
            'variables' => 'nullable|string', // JSON string dari JavaScript
            'custom_variables' => 'nullable|array',
            'custom_variables.*.name' => 'nullable|string|max:255',
            'custom_variables.*.label' => 'nullable|string', // No max length - bisa panjang untuk soal
            'custom_variables.*.type' => 'nullable|string|in:text,textarea,checkbox,radio,select,number,email,date,file,signature_pad',
            'custom_variables.*.options' => 'nullable|string', // No max length - bisa panjang untuk options
            'custom_variables.*.required' => 'nullable|boolean',
            'custom_variables.*.role' => 'nullable|string|in:asesi,asesor,both',
            'field_configurations' => 'nullable|string',
            'field_mappings' => 'nullable|string',
        ]);

        // Upload file
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalFilename = $file->getClientOriginalName();

            // Generate unique filename
            $filename = 'bank-soal/' . time() . '_' . Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

            // Store file to public disk
            $path = Storage::disk('public')->putFileAs('', $file, $filename);

            // Parse variables from JSON
            $variables = null;
            if ($request->filled('variables')) {
                $variables = json_decode($request->variables, true);
            }

            // Parse field configurations
            $fieldConfigurations = null;
            if ($request->filled('field_configurations')) {
                $fieldConfigurations = json_decode($request->field_configurations, true);
            }

            // Parse field mappings
            $fieldMappings = null;
            if ($request->filled('field_mappings')) {
                $fieldMappings = json_decode($request->field_mappings, true);
            }

            // Process custom variables - filter out empty entries
            $customVars = $request->custom_variables ?? [];
            $filteredCustomVars = array_filter($customVars, fn($var) => !empty($var['name']) && !empty($var['label']));
            $customVariables = !empty($filteredCustomVars) ? array_values($filteredCustomVars) : null;

            // Generate nama default jika tidak ada
            $skema = Skema::find($request->skema_id);
            $nama = $request->nama ?? ($skema ? $skema->nama . ' - ' . $request->tipe . ' - ' . ucfirst($request->target) : $request->tipe . ' - ' . ucfirst($request->target));

            // Create bank soal record
            BankSoal::create([
                'skema_id' => $request->skema_id,
                'nama' => $nama,
                'tipe' => $request->tipe,
                'target' => $request->target,
                'file_path' => $filename,
                'original_filename' => $originalFilename,
                'is_active' => true,
                'keterangan' => $request->keterangan,
                'variables' => $variables,
                'field_configurations' => $fieldConfigurations,
                'field_mappings' => $fieldMappings,
                'custom_variables' => $customVariables
            ]);

            return redirect()->route('admin.bank-soal.index')
                ->with('success', 'Bank soal berhasil ditambahkan');
        }

        return redirect()->back()
            ->with('error', 'Gagal mengupload file')
            ->withInput();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lists = $this->getMenuListAdmin('bank-soal');
        $activeMenu = 'bank-soal';

        $bankSoal = BankSoal::findOrFail($id);
        $skemas = Skema::orderBy('nama', 'asc')->get();

        $tipeOptions = [
            'FR IA 03' => 'FR IA 03 - Formulir Asesmen Mandiri',
            'FR IA 06' => 'FR IA 06 - Formulir Asesmen Praktik',
            'FR IA 07' => 'FR IA 07 - Ceklis Observasi Asesor'
        ];

        $targetOptions = [
            'asesi' => 'Untuk Asesi',
            'asesor' => 'Untuk Asesor'
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
        ];

        return view('components.pages.admin.bank-soal.edit', compact('lists', 'activeMenu', 'bankSoal', 'skemas', 'tipeOptions', 'targetOptions', 'availableFields'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Log::info('Bank Soal Update: Start', [
            'id' => $id,
            'all_input' => $request->except(['file', '_token']),
            'has_file' => $request->hasFile('file')
        ]);

        // Build validation rules
        $rules = [
            'skema_id' => 'required|exists:skema,id',
            'nama' => 'nullable|string|max:255',
            'tipe' => 'required|in:FR IA 03,FR IA 06,FR IA 07',
            'target' => 'required|in:asesi,asesor',
            'keterangan' => 'nullable|string',
        ];

        $messages = [
            'skema_id.required' => 'Skema Sertifikasi wajib dipilih.',
            'tipe.required' => 'Tipe Formulir wajib dipilih.',
            'target.required' => 'Target Pengguna wajib dipilih.',
        ];

        // Validasi: Skema Sertifikasi, Tipe Formulir, dan Target Pengguna hanya ada 1
        $existing = BankSoal::where('skema_id', $request->skema_id)
            ->where('tipe', $request->tipe)
            ->where('target', $request->target)
            ->where('id', '!=', $id)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Bank soal dengan kombinasi Skema Sertifikasi, Tipe Formulir, dan Target Pengguna yang sama sudah ada. Silakan edit bank soal yang sudah ada atau pilih kombinasi yang berbeda.');
        }

        $rules = array_merge($rules, [
            'keterangan' => 'nullable|string',
            'variables' => 'nullable|string', // JSON string dari JavaScript
            'custom_variables' => 'nullable|array',
            'custom_variables.*.name' => 'nullable|string|max:255',
            'custom_variables.*.label' => 'nullable|string', // No max length - bisa panjang untuk soal
            'custom_variables.*.type' => 'nullable|string|in:text,textarea,checkbox,radio,select,number,email,date,file,signature_pad',
            'custom_variables.*.options' => 'nullable|string', // No max length - bisa panjang untuk options
            'custom_variables.*.required' => 'nullable|boolean',
            'custom_variables.*.role' => 'nullable|string|in:asesi,asesor,both',
            'field_configurations' => 'nullable|string',
            'field_mappings' => 'nullable|string',
        ]);

        // Only validate file if it exists
        if ($request->hasFile('file')) {
            $rules['file'] = 'file|mimes:pdf,doc,docx|max:10240'; // max 10MB
        }

        $messages = array_merge($messages, [
            'file.max' => 'Ukuran file terlalu besar. Maksimal 10MB.',
            'file.mimes' => 'Format file tidak valid. Hanya menerima PDF, DOC, atau DOCX.',
            'file.file' => 'File yang diupload tidak valid.',
        ]);

        try {
            $request->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Bank Soal Update: Validation Failed', [
                'errors' => $e->errors(),
                'has_file' => $request->hasFile('file'),
                'file_input' => $request->file('file'),
                'file_info' => $request->hasFile('file') ? [
                    'name' => $request->file('file')->getClientOriginalName(),
                    'size' => $request->file('file')->getSize(),
                    'mime' => $request->file('file')->getMimeType(),
                ] : 'No file uploaded'
            ]);
            throw $e;
        }

        $bankSoal = BankSoal::findOrFail($id);

        // Update file if new file uploaded
        if ($request->hasFile('file')) {
            try {
                $file = $request->file('file');
                Log::info('Bank Soal Update: File detected', [
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ]);

                // Check if file is valid
                if (!$file->isValid()) {
                    throw new \Exception('File upload error: ' . $file->getErrorMessage());
                }

                // Delete old file if exists
                if ($bankSoal->file_path && Storage::disk('public')->exists($bankSoal->file_path)) {
                    Storage::disk('public')->delete($bankSoal->file_path);
                    Log::info('Bank Soal Update: Old file deleted', ['old_path' => $bankSoal->file_path]);
                }

                $originalFilename = $file->getClientOriginalName();

                // Generate unique filename
                $filename = 'bank-soal/' . time() . '_' . Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

                // Store new file to public disk
                $path = Storage::disk('public')->putFileAs('', $file, $filename);

                Log::info('Bank Soal Update: New file stored', [
                    'filename' => $filename,
                    'storage_path' => $path,
                    'full_path' => storage_path('app/public/' . $filename)
                ]);

                $bankSoal->file_path = $filename;
                $bankSoal->original_filename = $originalFilename;
            } catch (\Exception $e) {
                Log::error('Bank Soal Update: File upload failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                return redirect()->back()
                    ->with('error', 'Gagal upload file: ' . $e->getMessage() . '. Pastikan ukuran file < 10MB dan format PDF/DOC/DOCX.')
                    ->withInput();
            }
        } else {
            Log::info('Bank Soal Update: No file uploaded');
        }

        // Parse variables from JSON
        $variables = null;
        if ($request->filled('variables')) {
            $variables = json_decode($request->variables, true);
            Log::info('Bank Soal Update: Variables parsed', ['variables' => $variables]);
        }

        // Parse field configurations
        $fieldConfigurations = null;
        if ($request->filled('field_configurations')) {
            $fieldConfigurations = json_decode($request->field_configurations, true);
            Log::info('Bank Soal Update: Field configurations parsed', ['field_configurations' => $fieldConfigurations]);
        }

        // Parse field mappings
        $fieldMappings = null;
        if ($request->filled('field_mappings')) {
            $fieldMappings = json_decode($request->field_mappings, true);
            Log::info('Bank Soal Update: Field mappings parsed', ['field_mappings' => $fieldMappings]);
        }

        // Update other fields
        $bankSoal->skema_id = $request->skema_id;
        // Generate nama default jika tidak ada
        $skema = Skema::find($request->skema_id);
        $nama = $request->nama ?? ($skema ? $skema->nama . ' - ' . $request->tipe . ' - ' . ucfirst($request->target) : $request->tipe . ' - ' . ucfirst($request->target));
        $bankSoal->nama = $nama;
        $bankSoal->tipe = $request->tipe;
        $bankSoal->target = $request->target;
        $bankSoal->keterangan = $request->keterangan;
        $bankSoal->variables = $variables;
        $bankSoal->field_configurations = $fieldConfigurations;
        $bankSoal->field_mappings = $fieldMappings;

        // Process custom variables - filter out empty entries
        $customVars = $request->custom_variables ?? [];
        $filteredCustomVars = array_filter($customVars, function($var) {
            return !empty($var['name']) && !empty($var['label']);
        });
        $bankSoal->custom_variables = !empty($filteredCustomVars) ? array_values($filteredCustomVars) : null;

        try {
            $bankSoal->save();

            Log::info('Bank Soal Update: Saved successfully', [
                'id' => $bankSoal->id,
                'variables' => $variables,
                'custom_variables' => $bankSoal->custom_variables
            ]);

            return redirect()->route('admin.bank-soal.index')
                ->with('success', 'Bank soal berhasil diupdate');
        } catch (\Exception $e) {
            Log::error('Bank Soal Update: Save Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal mengupdate bank soal: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bankSoal = BankSoal::findOrFail($id);

        // Delete file from storage
        Storage::delete('public/' . $bankSoal->file_path);

        // Delete record
        $bankSoal->delete();

        return redirect()->route('admin.bank-soal.index')
            ->with('success', 'Bank soal berhasil dihapus');
    }

    /**
     * Download bank soal file
     */
    public function download(string $id)
    {
        $bankSoal = BankSoal::findOrFail($id);

        $filePath = storage_path('app/public/' . $bankSoal->file_path);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan');
        }

        return response()->download($filePath, $bankSoal->original_filename);
    }

    /**
     * Toggle status aktif/nonaktif
     */
    public function toggleStatus(string $id)
    {
        $bankSoal = BankSoal::findOrFail($id);
        $bankSoal->is_active = !$bankSoal->is_active;
        $bankSoal->save();

        $status = $bankSoal->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Bank soal berhasil {$status}");
    }
}
