<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Services\SecondRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegistrationInfoController extends Controller
{
    protected $secondRegistrationService;

    public function __construct(SecondRegistrationService $secondRegistrationService)
    {
        $this->secondRegistrationService = $secondRegistrationService;
    }

    /**
     * Get registration information for current user
     */
    public function index(Request $request)
    {
        try {
            $userId = Auth::id();
            $registrationInfo = $this->secondRegistrationService->getSecondRegistrationInfo($userId);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $registrationInfo
                ]);
            }

            return view('components.pages.asesi.registration-info', compact('registrationInfo'));
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error mengambil informasi pendaftaran: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error mengambil informasi pendaftaran: ' . $e->getMessage());
        }
    }
}
