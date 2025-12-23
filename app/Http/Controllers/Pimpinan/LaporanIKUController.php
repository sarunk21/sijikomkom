<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LaporanIKUController extends Controller
{
    use MenuTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $lists = $this->getMenuListPimpinan('laporan-iku');

        $query = Report::where('status', 1)
            ->with(['user', 'skema', 'pendaftaran.pendaftaranUjikom.asesor']);

        // Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('skema_id')) {
            $query->where('skema_id', $request->skema_id);
        }

        if ($request->filled('asesor_id')) {
            $query->whereHas('pendaftaran.pendaftaranUjikom', function($q) use ($request) {
                $q->where('asesor_id', $request->asesor_id);
            });
        }

        $reports = $query->orderBy('created_at', 'desc')->get();

        // Get all skema for filter dropdown
        $skemas = \App\Models\Skema::orderBy('nama', 'asc')->get();

        // Get all asesor yang pernah mengases (dari pendaftaran_ujikom yang memiliki report status 1)
        $asesorIds = \App\Models\PendaftaranUjikom::whereHas('pendaftaran.report', function($q) {
                $q->where('status', 1);
            })
            ->distinct()
            ->pluck('asesor_id')
            ->filter();

        $asesors = \App\Models\User::where('user_type', 'asesor')
            ->whereIn('id', $asesorIds)
            ->orderBy('name', 'asc')
            ->get();

        return view('components.pages.pimpinan.laporan-iku.list', compact('lists', 'reports', 'skemas', 'asesors'));
    }

    /**
     * Export Laporan IKU 2 ke Excel dengan filter yang sama seperti index.
     */
    public function exportExcel(Request $request)
    {
        try {
            $query = Report::where('status', 1)
                ->with(['user', 'skema', 'pendaftaran.pendaftaranUjikom.asesor']);

            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            if ($request->filled('skema_id')) {
                $query->where('skema_id', $request->skema_id);
            }

            if ($request->filled('asesor_id')) {
                $query->whereHas('pendaftaran.pendaftaranUjikom', function($q) use ($request) {
                    $q->where('asesor_id', $request->asesor_id);
                });
            }

            $reports = $query->orderBy('created_at', 'desc')->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set title
            $sheet->setCellValue('A1', 'LAPORAN IKU');
            $sheet->mergeCells('A1:F1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Info filter
            $sheet->setCellValue('A2', 'Periode: ' . ($request->start_date ?? '-') . ' s/d ' . ($request->end_date ?? '-'));

            // Set headers
            $headers = ['No', 'NIM', 'Nama', 'Skema', 'Prodi', 'Asesor'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '4', $header);
                $col++;
            }

            // Style headers
            $headerRange = 'A4:F4';
            $sheet->getStyle($headerRange)->getFont()->setBold(true);
            $sheet->getStyle($headerRange)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF4472C4');
            $sheet->getStyle($headerRange)->getFont()->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($headerRange)->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);

            // Fill data
            $row = 5;
            $no = 1;
            foreach ($reports as $item) {
                $asesorName = '';
                if ($item->pendaftaran && $item->pendaftaran->pendaftaranUjikom && $item->pendaftaran->pendaftaranUjikom->asesor) {
                    $asesorName = $item->pendaftaran->pendaftaranUjikom->asesor->name ?? '';
                }

                $sheet->setCellValue('A' . $row, $no);
                $sheet->setCellValue('B' . $row, $item->user ? ($item->user->nim ?? '') : '');
                $sheet->setCellValue('C' . $row, $item->user ? ($item->user->name ?? '') : '');
                $sheet->setCellValue('D' . $row, $item->skema ? ($item->skema->nama ?? '') : '');
                $sheet->setCellValue('E' . $row, $item->user ? ($item->user->jurusan ?? '') : '');
                $sheet->setCellValue('F' . $row, $asesorName);

                // Style data rows
                $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $row++;
                $no++;
            }

            // Auto size columns
            foreach (range('A', 'F') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Set row height for header
            $sheet->getRowDimension(4)->setRowHeight(20);

            $writer = new Xlsx($spreadsheet);
            $filename = 'Laporan_IKU_' . date('Y-m-d_His') . '.xlsx';

            // Save to temporary file
            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            $tempPath = $tempDir . '/' . $filename;

            $writer->save($tempPath);

            // Return download response
            return response()->download($tempPath, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Export Excel Laporan IKU 2 Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
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
}
