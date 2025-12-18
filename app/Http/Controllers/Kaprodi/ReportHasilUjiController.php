<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReportHasilUjiController extends Controller
{
    use MenuTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $lists = $this->getMenuListKaprodi('report-hasil-uji');

        $query = Jadwal::where('status', 4)
            ->with(['skema', 'tuk']);

        // Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_ujian', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_ujian', '<=', $request->end_date);
        }

        if ($request->filled('skema_id')) {
            $query->where('skema_id', $request->skema_id);
        }

        if ($request->filled('tuk_id')) {
            $query->where('tuk_id', $request->tuk_id);
        }

        $reports = $query->orderBy('tanggal_ujian', 'asc')->get();

        // Get all skema and tuk for filter dropdowns
        $skemas = \App\Models\Skema::orderBy('nama', 'asc')->get();
        $tuks = \App\Models\Tuk::orderBy('nama', 'asc')->get();

        return view('components.pages.kaprodi.report-hasil-uji.list', compact('lists', 'reports', 'skemas', 'tuks'));
    }

    /**
     * Export report to Excel
     */
    public function exportExcel(Request $request)
    {
        $query = Jadwal::where('status', 4)
            ->with(['skema', 'tuk']);

        // Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_ujian', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_ujian', '<=', $request->end_date);
        }

        if ($request->filled('skema_id')) {
            $query->where('skema_id', $request->skema_id);
        }

        if ($request->filled('tuk_id')) {
            $query->where('tuk_id', $request->tuk_id);
        }

        $reports = $query->orderBy('tanggal_ujian', 'asc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'LAPORAN HASIL UJIKOM');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set headers
        $headers = ['No', 'Skema', 'Jumlah Asesi', 'Tanggal Ujian', 'Jumlah Kompeten', 'Jumlah Tidak Kompeten'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '3', $header);
            $col++;
        }

        // Style headers
        $headerRange = 'A3:F3';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF4472C4');
        $sheet->getStyle($headerRange)->getFont()->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Fill data
        $row = 4;
        $no = 1;
        foreach ($reports as $item) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item->skema->nama ?? '');
            $sheet->setCellValue('C' . $row, $item->jumlah_asesi()->count());
            $sheet->setCellValue('D' . $row, $item->tanggal_ujian);
            $sheet->setCellValue('E' . $row, $item->jumlah_kompeten()->count());
            $sheet->setCellValue('F' . $row, $item->jumlah_tidak_kompeten()->count());

            // Style data rows
            $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row . ':F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
            $no++;
        }

        // Auto size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set row height for header
        $sheet->getRowDimension(3)->setRowHeight(20);

        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan_Hasil_Ujikom_' . date('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
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
    public function store(Request $request)
    {
        //
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

    public function listNamaKompeten(string $id)
    {
        $lists = $this->getMenuListKaprodi('report-hasil-uji');

        $reports = Jadwal::find($id)->jumlah_kompeten()->get();
        $reports = $reports->map(function ($report) {
            return [
                'skema' => $report->skema->nama,
                'nama' => $report->user->name,
                'nim' => $report->user->nim,
            ];
        });

        return view('components.pages.kaprodi.report-hasil-uji.list-nama-kompeten', compact('lists', 'reports'));
    }

    public function listNamaTidakKompeten(string $id)
    {
        $lists = $this->getMenuListKaprodi('report-hasil-uji');

        $reports = Jadwal::find($id)->jumlah_tidak_kompeten()->get();
        $reports = $reports->map(function ($report) {
            return [
                'skema' => $report->skema->nama,
                'nama' => $report->user->name,
                'nim' => $report->user->nim,
            ];
        });

        return view('components.pages.kaprodi.report-hasil-uji.list-nama-tidak-kompeten', compact('lists', 'reports'));
    }

    /**
     * Export daftar asesi kompeten ke Excel untuk satu jadwal (kaprodi).
     */
    public function exportNamaKompetenExcel(string $id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $reports = $jadwal->jumlah_kompeten()->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'DAFTAR ASESİ KOMPETEN');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Skema: ' . ($jadwal->skema->nama ?? '-'));
        $sheet->setCellValue('C2', 'Tanggal Ujian: ' . $jadwal->tanggal_ujian);

        $headers = ['No', 'Skema', 'Nama', 'NIM'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $col++;
        }

        $headerRange = 'A4:D4';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF4472C4');
        $sheet->getStyle($headerRange)->getFont()->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        $row = 5;
        $no = 1;
        foreach ($reports as $report) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $report->skema->nama ?? '');
            $sheet->setCellValue('C' . $row, $report->user->name ?? '');
            $sheet->setCellValue('D' . $row, $report->user->nim ?? '');

            $sheet->getStyle('A' . $row . ':D' . $row)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
            $no++;
        }

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getRowDimension(4)->setRowHeight(20);

        $writer = new Xlsx($spreadsheet);
        $filename = 'Daftar_Asesi_Kompeten_Kaprodi_' . $jadwal->id . '_' . date('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export daftar asesi tidak kompeten ke Excel untuk satu jadwal (kaprodi).
     */
    public function exportNamaTidakKompetenExcel(string $id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $reports = $jadwal->jumlah_tidak_kompeten()->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'DAFTAR ASESİ TIDAK KOMPETEN');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', 'Skema: ' . ($jadwal->skema->nama ?? '-'));
        $sheet->setCellValue('C2', 'Tanggal Ujian: ' . $jadwal->tanggal_ujian);

        $headers = ['No', 'Skema', 'Nama', 'NIM'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $col++;
        }

        $headerRange = 'A4:D4';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF4472C4');
        $sheet->getStyle($headerRange)->getFont()->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        $row = 5;
        $no = 1;
        foreach ($reports as $report) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $report->skema->nama ?? '');
            $sheet->setCellValue('C' . $row, $report->user->name ?? '');
            $sheet->setCellValue('D' . $row, $report->user->nim ?? '');

            $sheet->getStyle('A' . $row . ':D' . $row)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
            $no++;
        }

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getRowDimension(4)->setRowHeight(20);

        $writer = new Xlsx($spreadsheet);
        $filename = 'Daftar_Asesi_Tidak_Kompeten_Kaprodi_' . $jadwal->id . '_' . date('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
