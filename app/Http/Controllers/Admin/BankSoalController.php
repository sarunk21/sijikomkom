<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankSoal;
use App\Models\Skema;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
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
            'FR AI 03' => 'FR AI 03 - Formulir Asesmen Mandiri',
            'FR AI 06' => 'FR AI 06 - Formulir Asesmen Praktik',
            'FR AI 07' => 'FR AI 07 - Ceklis Observasi Asesor'
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
            'nama' => 'required|string|max:255',
            'tipe' => 'required|in:FR AI 03,FR AI 06,FR AI 07',
            'target' => 'required|in:asesi,asesor',
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240', // max 10MB
            'keterangan' => 'nullable|string',
            'variables' => 'nullable|string', // JSON string dari JavaScript
            'custom_variables' => 'nullable|array',
            'custom_variables.*.name' => 'nullable|string|max:255',
            'custom_variables.*.label' => 'nullable|string|max:255',
            'custom_variables.*.type' => 'nullable|string|in:text,textarea,checkbox,radio,select,number,email,date,file,signature_pad',
            'custom_variables.*.options' => 'nullable|string',
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

            // Store file
            $path = $file->storeAs('public', $filename);

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

            // Create bank soal record
            BankSoal::create([
                'skema_id' => $request->skema_id,
                'nama' => $request->nama,
                'tipe' => $request->tipe,
                'target' => $request->target,
                'file_path' => $filename,
                'original_filename' => $originalFilename,
                'is_active' => true,
                'keterangan' => $request->keterangan,
                'variables' => $variables,
                'field_configurations' => $fieldConfigurations,
                'field_mappings' => $fieldMappings,
                'custom_variables' => $request->custom_variables
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
            'FR AI 03' => 'FR AI 03 - Formulir Asesmen Mandiri',
            'FR AI 06' => 'FR AI 06 - Formulir Asesmen Praktik',
            'FR AI 07' => 'FR AI 07 - Ceklis Observasi Asesor'
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
        $request->validate([
            'skema_id' => 'required|exists:skema,id',
            'nama' => 'required|string|max:255',
            'tipe' => 'required|in:FR AI 03,FR AI 06,FR AI 07',
            'target' => 'required|in:asesi,asesor',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // max 10MB
            'keterangan' => 'nullable|string',
            'variables' => 'nullable|string', // JSON string dari JavaScript
            'custom_variables' => 'nullable|array',
            'custom_variables.*.name' => 'nullable|string|max:255',
            'custom_variables.*.label' => 'nullable|string|max:255',
            'custom_variables.*.type' => 'nullable|string|in:text,textarea,checkbox,radio,select,number,email,date,file,signature_pad',
            'custom_variables.*.options' => 'nullable|string',
            'custom_variables.*.required' => 'nullable|boolean',
            'custom_variables.*.role' => 'nullable|string|in:asesi,asesor,both',
            'field_configurations' => 'nullable|string',
            'field_mappings' => 'nullable|string',
        ]);

        $bankSoal = BankSoal::findOrFail($id);

        // Update file if new file uploaded
        if ($request->hasFile('file')) {
            // Delete old file
            Storage::delete('public/' . $bankSoal->file_path);

            $file = $request->file('file');
            $originalFilename = $file->getClientOriginalName();

            // Generate unique filename
            $filename = 'bank-soal/' . time() . '_' . Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

            // Store new file
            $path = $file->storeAs('public', $filename);

            $bankSoal->file_path = $filename;
            $bankSoal->original_filename = $originalFilename;
        }

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

        // Update other fields
        $bankSoal->skema_id = $request->skema_id;
        $bankSoal->nama = $request->nama;
        $bankSoal->tipe = $request->tipe;
        $bankSoal->target = $request->target;
        $bankSoal->keterangan = $request->keterangan;
        $bankSoal->variables = $variables;
        $bankSoal->field_configurations = $fieldConfigurations;
        $bankSoal->field_mappings = $fieldMappings;
        $bankSoal->custom_variables = $request->custom_variables;
        $bankSoal->save();

        return redirect()->route('admin.bank-soal.index')
            ->with('success', 'Bank soal berhasil diupdate');
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
