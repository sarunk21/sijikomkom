<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Pembayaran;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Services\SecondRegistrationService;
use App\Traits\MenuTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DaftarUjikomController extends Controller
{
    use MenuTrait;

    protected $secondRegistrationService;

    public function __construct(SecondRegistrationService $secondRegistrationService)
    {
        $this->secondRegistrationService = $secondRegistrationService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Cek asesi harus melengkapi profile
        $asesi = User::where('id', Auth::user()->id)->first();
        if (!$asesi->checkProfileLengkapAsesi()) {
            return redirect()->route('asesi.profil-asesi.index')->with('error', 'Asesi harus melengkapi profil');
        }

        // Cek apakah ini pendaftaran kedua
        $isSecondRegistration = $request->has('second_registration');
        $registrationInfo = $this->secondRegistrationService->getSecondRegistrationInfo();

        // Cek apakah user bisa mendaftar lagi
        if (!$this->secondRegistrationService->canRegisterAgain()) {
            $lastPayment = $registrationInfo['last_payment'];
            if ($lastPayment && in_array($lastPayment->status, [1, 2])) {
                return redirect()->route('asesi.informasi-pembayaran.index')
                    ->with('warning', 'Anda memiliki pembayaran yang belum diselesaikan. Silakan selesaikan pembayaran terlebih dahulu.');
            }
        }

        $jadwal = Jadwal::with('skema', 'tuk')
            ->whereHas('skema', function ($query) use ($asesi) {
                $query->where('bidang', $asesi->jurusan);
            })
            ->where('status', 1)
            ->where('tanggal_maksimal_pendaftaran', '>=', now())
            ->orderBy('tanggal_maksimal_pendaftaran', 'asc')
            ->get();

        $lists = $this->getMenuListAsesi('daftar-ujikom');
        return view('components.pages.asesi.daftar-ujikom.index', compact('lists', 'jadwal', 'registrationInfo', 'isSecondRegistration'));
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
        $user = User::where('id', Auth::user()->id)->first();

        // Dynamic validation rules based on existing files
        // Accept both images (jpeg, png, jpg) and PDF files
        $rules = [
            'jadwal_id' => 'required|exists:jadwal,id',
            'photo_ktp' => ($user->photo_ktp ? 'nullable' : 'required') . '|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'photo_sertifikat' => ($user->photo_sertifikat ? 'nullable' : 'required') . '|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'photo_ktmkhs' => ($user->photo_ktmkhs ? 'nullable' : 'required') . '|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'photo_administatif' => ($user->photo_administatif ? 'nullable' : 'required') . '|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ];

        $request->validate($rules);

        // Prepare data to update
        $updateData = [];

        // Only update files that are uploaded
        if ($request->hasFile('photo_ktp')) {
            $updateData['photo_ktp'] = $request->file('photo_ktp')->store('photos', 'public');
        }
        if ($request->hasFile('photo_sertifikat')) {
            $updateData['photo_sertifikat'] = $request->file('photo_sertifikat')->store('photos', 'public');
        }
        if ($request->hasFile('photo_ktmkhs')) {
            $updateData['photo_ktmkhs'] = $request->file('photo_ktmkhs')->store('photos', 'public');
        }
        if ($request->hasFile('photo_administatif')) {
            $updateData['photo_administatif'] = $request->file('photo_administatif')->store('photos', 'public');
        }

        // Update user only if there are files to update
        if (!empty($updateData)) {
            $user->update($updateData);
        }

        // Gunakan service untuk membuat pembayaran
        try {
            $pembayaran = $this->secondRegistrationService->createSecondRegistrationPayment($request->jadwal_id);

            // Set session untuk menampilkan informasi pembayaran
            session()->flash('payment_status_message', 'Pendaftaran berhasil! Silakan lakukan pembayaran dan upload bukti pembayaran.');
            session()->flash('payment_status_type', 'success');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error saat membuat pembayaran: ' . $e->getMessage());
        }

        return redirect()->route('asesi.informasi-pembayaran.index')->with('success', 'Berhasil daftar ujikom');
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
