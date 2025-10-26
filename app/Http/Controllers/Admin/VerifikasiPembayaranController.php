<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Services\SecondRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerifikasiPembayaranController extends Controller
{
    protected $secondRegistrationService;

    public function __construct(SecondRegistrationService $secondRegistrationService)
    {
        $this->secondRegistrationService = $secondRegistrationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pembayaran = Pembayaran::with(['jadwal', 'jadwal.skema', 'jadwal.tuk', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('components.pages.admin.verifikasi-pembayaran.index', compact('pembayaran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pembayaran = Pembayaran::with(['jadwal', 'jadwal.skema', 'jadwal.tuk', 'user'])
            ->findOrFail($id);

        return view('components.pages.admin.verifikasi-pembayaran.edit', compact('pembayaran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:2,3,4',
            'keterangan' => 'nullable|string|max:255'
        ]);

        try {
            $pembayaran = $this->secondRegistrationService->verifySecondRegistrationPayment(
                $id,
                $request->status,
                $request->keterangan
            );

            $statusText = $pembayaran->status_text;
            $message = "Pembayaran berhasil diverifikasi dengan status: {$statusText}";

            return redirect()->route('admin.verifikasi-pembayaran.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error saat memverifikasi pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Approve payment
     */
    public function approve(Request $request, string $id)
    {
        $request->merge(['status' => 4, 'keterangan' => 'Pembayaran disetujui oleh admin']);
        return $this->update($request, $id);
    }

    /**
     * Reject payment
     */
    public function reject(Request $request, string $id)
    {
        $request->validate([
            'keterangan' => 'required|string|max:255'
        ]);

        $request->merge(['status' => 3]);
        return $this->update($request, $id);
    }
}
