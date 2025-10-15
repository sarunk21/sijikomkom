<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Skema;
use App\Models\AsesorSkema;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserController extends Controller
{
    use MenuTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('skemas')->orderBy('name', 'asc')->get();
        $lists = $this->getMenuListAdmin('user');
        return view('components.pages.admin.user.list', compact('lists', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lists = $this->getMenuListAdmin('user');
        $activeMenu = 'user';
        return view('components.pages.admin.user.create', compact('lists', 'activeMenu'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'nik' => 'required|unique:users,nik,NULL,id,deleted_at,NULL',
            'telephone' => 'required|unique:users,telephone,NULL,id,deleted_at,NULL',
            'user_type' => 'required|in:asesi,asesor,asesor_nonaktif,kaprodi,pimpinan,admin',
            'alamat' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->email),
                'nik' => $request->nik,
                'telephone' => $request->telephone,
                'alamat' => $request->alamat,
                'user_type' => $request->user_type,
            ]);


            DB::commit();
            return redirect()->route('admin.user.index')->with('success', 'User berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.user.index')->with('error', 'User gagal ditambahkan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with('skemas')->find($id);
        $lists = $this->getMenuListAdmin('user');
        $activeMenu = 'user';
        $skemas = Skema::orderBy('nama', 'asc')->get();
        return view('components.pages.admin.user.edit', compact('lists', 'activeMenu', 'user', 'skemas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lists = $this->getMenuListAdmin('user');
        $activeMenu = 'user';
        $user = User::find($id);
        return view('components.pages.admin.user.edit', compact('lists', 'activeMenu', 'user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id . ',id,deleted_at,NULL',
            'nik' => 'required|unique:users,nik,' . $id . ',id,deleted_at,NULL',
            'telephone' => 'required|unique:users,telephone,' . $id . ',id,deleted_at,NULL',
            'alamat' => 'required',
            'skemas' => 'nullable|array',
            'skemas.*' => 'exists:skema,id',
        ]);

        DB::beginTransaction();
        try {
            $user = User::find($id);
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->email),
                'nik' => $request->nik,
                'telephone' => $request->telephone,
                'alamat' => $request->alamat,
                'user_type' => $request->user_type,
            ]);

            // Update skema untuk asesor
            if (in_array($request->user_type, ['asesor', 'asesor_nonaktif'])) {
                // Hapus skema lama
                AsesorSkema::where('asesor_id', $user->id)->delete();

                // Tambah skema baru
                if ($request->has('skemas') && is_array($request->skemas)) {
                    foreach ($request->skemas as $skemaId) {
                        AsesorSkema::create([
                            'asesor_id' => $user->id,
                            'skema_id' => $skemaId,
                        ]);
                    }
                }
            } else {
                // Jika bukan asesor, hapus semua skema yang terkait
                AsesorSkema::where('asesor_id', $user->id)->delete();
            }

            DB::commit();
            return redirect()->route('admin.user.index')->with('success', 'User berhasil diubah');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.user.index')->with('error', 'User gagal diubah: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        try {
            $user->delete();
            return redirect()->route('admin.user.index')->with('success', 'User berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.user.index')->with('error', 'User gagal dihapus');
        }
    }

    public function nonaktifkan(string $id)
    {
        $user = User::find($id);
        $user->update(['user_type' => 'asesor_nonaktif']);
        return redirect()->route('admin.user.index')->with('success', 'User berhasil dinonaktifkan');
    }

    public function aktifkan(string $id)
    {
        $user = User::find($id);
        $user->update(['user_type' => 'asesor']);
        return redirect()->route('admin.user.index')->with('success', 'User berhasil diaktifkan');
    }

    /**
     * Import users from uploaded CSV file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();
        $extension = strtolower($file->getClientOriginalExtension());

        $header = null;
        $rowsIterator = null; // akan berupa iterator baris data tanpa header
        $handle = null; // untuk CSV

        if (in_array($extension, ['csv', 'txt'])) {
            if (($handle = fopen($path, 'r')) === false) {
                return back()->with('error', 'Tidak dapat membuka file');
            }

            // Baca baris pertama, jika berisi sep=, skip
            $firstLine = fgets($handle);
            if ($firstLine === false) {
                fclose($handle);
                return back()->with('error', 'File kosong');
            }
            $firstLineTrim = trim($firstLine, "\xEF\xBB\xBF\r\n");
            if (stripos($firstLineTrim, 'sep=,') === 0) {
                // baca header sebenarnya dari baris berikutnya
                $header = fgetcsv($handle, 0, ',');
            } else {
                // baris pertama adalah header; parse ulang sebagai CSV
                $header = str_getcsv($firstLineTrim, ',');
            }

            if (!$header) {
                fclose($handle);
                return back()->with('error', 'Header tidak valid');
            }

            // define rows iterator untuk CSV
            $rowsIterator = function () use ($handle) {
                while (($row = fgetcsv($handle, 0, ',')) !== false) {
                    yield $row;
                }
            };
        } elseif (in_array($extension, ['xlsx', 'xls'])) {
            try {
                $spreadsheet = IOFactory::load($path);
                $sheet = $spreadsheet->getActiveSheet();
                $all = $sheet->toArray(null, false, true, false); // nulls ok, no calc, formatted, indexed
            } catch (\Throwable $e) {
                return back()->with('error', 'Tidak dapat membaca Excel: ' . $e->getMessage());
            }

            if (empty($all) || empty($all[0])) {
                return back()->with('error', 'Sheet kosong atau header tidak ditemukan');
            }

            $header = $all[0];
            $dataRows = array_slice($all, 1);
            $rowsIterator = function () use ($dataRows) {
                foreach ($dataRows as $row) {
                    yield $row;
                }
            };
        } else {
            return back()->with('error', 'Format file tidak didukung');
        }

        $requiredHeader = [
            'name','nik','nim','telephone','email','tempat_lahir','tanggal_lahir','jenis_kelamin','alamat','kebangsaan','pekerjaan','pendidikan','jurusan','user_type'
        ];

        // Alias header agar bisa menerima versi "rapi" (Capital, spasi) maupun versi sistem (snake_case)
        $headerAliases = [
            'name' => ['name', 'nama', 'Name', 'Nama'],
            'nik' => ['nik', 'NIK'],
            'nim' => ['nim', 'NIM'],
            'telephone' => ['telephone', 'telepon', 'no telepon', 'no. telepon', 'hp', 'no hp', 'no. hp', 'Telephone', 'Telepon', 'No Telepon', 'No. Telepon', 'HP', 'No HP', 'No. HP'],
            'email' => ['email', 'Email'],
            'tempat_lahir' => ['tempat_lahir', 'tempat lahir', 'Tempat Lahir'],
            'tanggal_lahir' => ['tanggal_lahir', 'tanggal lahir', 'Tanggal Lahir'],
            'jenis_kelamin' => ['jenis_kelamin', 'jenis kelamin', 'Jenis Kelamin', 'jk', 'JK'],
            'alamat' => ['alamat', 'Alamat'],
            'kebangsaan' => ['kebangsaan', 'Kebangsaan', 'kewarganegaraan', 'Kewarganegaraan', 'warga negara', 'Warga Negara'],
            'pekerjaan' => ['pekerjaan', 'Pekerjaan'],
            'pendidikan' => ['pendidikan', 'Pendidikan'],
            'jurusan' => ['jurusan', 'Jurusan', 'program studi', 'Program Studi', 'prodi', 'Prodi'],
            'user_type' => ['user_type', 'user type', 'User Type', 'role', 'Role'],
        ];

        // Bangun peta headerIndex -> canonicalKey
        $mapHeaderToKey = function (string $label) use ($headerAliases) {
            $labelNorm = strtolower(trim($label));
            $labelNorm = str_replace(['_', '-'], ' ', $labelNorm);
            foreach ($headerAliases as $key => $aliasList) {
                foreach ($aliasList as $alias) {
                    $aliasNorm = strtolower(trim(str_replace(['_', '-'], ' ', $alias)));
                    if ($labelNorm === $aliasNorm) {
                        return $key;
                    }
                }
            }
            return null;
        };

        $headerMap = [];
        foreach ($header as $idx => $label) {
            $key = $mapHeaderToKey($label);
            if ($key !== null) {
                $headerMap[$idx] = $key;
            }
        }

        // Pastikan semua kolom wajib ada
        $presentKeys = array_values(array_unique(array_values($headerMap)));
        sort($presentKeys);
        $requiredSorted = $requiredHeader;
        sort($requiredSorted);
        if ($presentKeys !== $requiredSorted) {
            fclose($handle);
            return back()->with('error', 'Header tidak lengkap/valid. Wajib ada: '.implode(', ', $requiredHeader));
        }

        $batch = [];
        $batchSize = 1000;
        $now = now();
        $inserted = 0;

        DB::beginTransaction();
        try {
            $clean = function ($value) {
                if ($value === null) return null;
                $value = trim($value);
                // Hilangkan satu tanda petik pembuka (umum dari Excel untuk Text)
                if (strlen($value) > 0 && $value[0] === "'") {
                    $value = substr($value, 1);
                }
                return $value === '' ? null : $value;
            };
            foreach ($rowsIterator() as $row) {
                // lewati baris kosong total
                $isEmpty = true;
                foreach ($row as $val) {
                    if ($val !== null && trim((string)$val) !== '') { $isEmpty = false; break; }
                }
                if ($isEmpty) { continue; }

                // susun data berdasar headerMap
                $data = [];
                foreach ($headerMap as $colIndex => $canonicalKey) {
                    $data[$canonicalKey] = $row[$colIndex] ?? null;
                }

                $batch[] = [
                    'name' => $clean($data['name']),
                    'nik' => $clean($data['nik']),
                    'nim' => $clean($data['nim']),
                    'telephone' => $clean($data['telephone']),
                    'email' => $clean($data['email']),
                    'tempat_lahir' => $clean($data['tempat_lahir']),
                    'tanggal_lahir' => $clean($data['tanggal_lahir']),
                    'jenis_kelamin' => $clean($data['jenis_kelamin']),
                    'alamat' => $clean($data['alamat']),
                    'kebangsaan' => $clean($data['kebangsaan']),
                    'pekerjaan' => $clean($data['pekerjaan']),
                    'pendidikan' => $clean($data['pendidikan']),
                    'jurusan' => $clean($data['jurusan']),
                    'user_type' => $clean($data['user_type']),
                    'password' => Hash::make($clean($data['email'])),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($batch) >= $batchSize) {
                    DB::table('users')->insert($batch);
                    $inserted += count($batch);
                    $batch = [];
                }
            }

            if (!empty($batch)) {
                DB::table('users')->insert($batch);
                $inserted += count($batch);
            }
            if ($handle) { fclose($handle); }
            DB::commit();
        } catch (\Throwable $e) {
            if ($handle) { fclose($handle); }
            DB::rollBack();
            return back()->with('error', 'Gagal import: '.$e->getMessage());
        }

        return redirect()->route('admin.user.index')->with('success', 'Import selesai. Total baris: '.$inserted);
    }

    /**
     * Download CSV template header.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_import_user.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            // Tambahkan BOM agar nyaman dibuka di Excel
            fprintf($handle, "\xEF\xBB\xBF");
            // Hint separator untuk Excel (agar tidak salah deteksi)
            fwrite($handle, "sep=,\n");

            $headerReadable = [
                'Name','NIK','NIM','Telephone','Email','Tempat Lahir','Tanggal Lahir','Jenis Kelamin','Alamat','Kebangsaan','Pekerjaan','Pendidikan','Jurusan','User Type'
            ];
            fputcsv($handle, $headerReadable);

            // Contoh baris 1 (gunakan prefix ' agar Excel tidak menghapus nol di depan)
            fputcsv($handle, [
                'Budi Santoso',
                "'3578123456789001",
                "'220123456",
                "'081234567890",
                'budi@example.com',
                'Surabaya',
                '1999-05-21',
                'L',
                'Jl. Mawar No. 12, Surabaya',
                'Indonesia',
                'Mahasiswa',
                'S1',
                'S1 Teknik Informatika',
                'asesi',
            ]);

            // Contoh baris 2
            fputcsv($handle, [
                'Siti Aminah',
                "'3276123456789002",
                '',
                "'081298765432",
                'siti.aminah@example.com',
                'Bandung',
                '1990-10-07',
                'P',
                'Jl. Melati No. 5, Bandung',
                'Indonesia',
                'Dosen',
                'S2',
                'S1 Sistem Informasi',
                'asesor',
            ]);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download Excel template (XLSX) dengan dropdown dan header readable.
     */
    public function downloadTemplateExcel()
    {
        $spreadsheet = new Spreadsheet();
        /** @var Worksheet $sheet */
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import User');

        // Header readable (capitalized, spasi)
        $headersReadable = [
            'Name','NIK','NIM','Telephone','Email','Tempat Lahir','Tanggal Lahir','Jenis Kelamin','Alamat','Kebangsaan','Pekerjaan','Pendidikan','Jurusan','User Type'
        ];

        // Tulis header
        $letters = range('A', 'Z');
        foreach ($headersReadable as $index => $label) {
            $cell = $letters[$index] . '1'; // A1..N1
            $sheet->setCellValue($cell, $label);
            $sheet->getStyle($cell)->getFont()->setBold(true);
        }

        // Contoh data (baris 2-3)
        $examples = [
            ['Budi Santoso', "3578123456789001", "220123456", "081234567890", 'budi@example.com', 'Surabaya', '1999-05-21', 'L', 'Jl. Mawar No. 12, Surabaya', 'Indonesia', 'Mahasiswa', 'S1', 'Teknik Informatika', 'asesi'],
            ['Siti Aminah',  "3276123456789002", "",           "081298765432", 'siti.aminah@example.com', 'Bandung', '1990-10-07', 'P', 'Jl. Melati No. 5, Bandung', 'Indonesia', 'Dosen',     'S2', 'Manajemen',         'asesor'],
        ];

        foreach ($examples as $rIndex => $row) {
            foreach ($row as $cIndex => $value) {
                $cell = $letters[$cIndex] . (string)($rIndex + 2);
                $sheet->setCellValue($cell, $value);
            }
        }

        // Lebar kolom otomatis
        foreach (range('A', 'N') as $colLetter) {
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Dropdown Jenis Kelamin (kolom H)
        $jkValidation = $sheet->getCell('H2')->getDataValidation();
        $jkValidation->setType(DataValidation::TYPE_LIST);
        $jkValidation->setErrorStyle(DataValidation::STYLE_STOP);
        $jkValidation->setAllowBlank(true);
        $jkValidation->setShowDropDown(true);
        $jkValidation->setFormula1('"L,P"');
        // Apply ke range H2:H1000
        for ($row = 2; $row <= 1000; $row++) {
            $sheet->getCell('H'.$row)->setDataValidation(clone $jkValidation);
        }

        // Dropdown Pendidikan (kolom L) — contoh opsi
        $pendidikanOpsi = '"SMA/SMK,D3,S1,S2,S3"';
        $pdValidation = $sheet->getCell('L2')->getDataValidation();
        $pdValidation->setType(DataValidation::TYPE_LIST);
        $pdValidation->setErrorStyle(DataValidation::STYLE_STOP);
        $pdValidation->setAllowBlank(true);
        $pdValidation->setShowDropDown(true);
        $pdValidation->setFormula1($pendidikanOpsi);
        for ($row = 2; $row <= 1000; $row++) {
            $sheet->getCell('L'.$row)->setDataValidation(clone $pdValidation);
        }

        // Dropdown Jurusan (kolom M) — contoh opsi dari snippet
        $jurusanOpsi = '"S1 Sistem Informasi,S1 Teknik Informatika,D3 Sistem Informasi,Manajemen"';
        $jrValidation = $sheet->getCell('M2')->getDataValidation();
        $jrValidation->setType(DataValidation::TYPE_LIST);
        $jrValidation->setErrorStyle(DataValidation::STYLE_STOP);
        $jrValidation->setAllowBlank(true);
        $jrValidation->setShowDropDown(true);
        $jrValidation->setFormula1($jurusanOpsi);
        for ($row = 2; $row <= 1000; $row++) {
            $sheet->getCell('M'.$row)->setDataValidation(clone $jrValidation);
        }

        // Dropdown User Type (kolom N)
        $roleOpsi = '"asesi,asesor,asesor_nonaktif,kaprodi,pimpinan,admin"';
        $utValidation = $sheet->getCell('N2')->getDataValidation();
        $utValidation->setType(DataValidation::TYPE_LIST);
        $utValidation->setErrorStyle(DataValidation::STYLE_STOP);
        $utValidation->setAllowBlank(false);
        $utValidation->setShowDropDown(true);
        $utValidation->setFormula1($roleOpsi);
        for ($row = 2; $row <= 1000; $row++) {
            $sheet->getCell('N'.$row)->setDataValidation(clone $utValidation);
        }

        // Format kolom angka sebagai teks agar nol depan tidak hilang (NIK, NIM, Telephone)
        foreach (['B','C','D'] as $letter) {
            $sheet->getStyle($letter.'2:'.$letter.'1000')->getNumberFormat()->setFormatCode('@');
        }

        // Output file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'template_import_user.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
