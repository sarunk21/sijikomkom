<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class AnalyticsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'skema_id' => 'nullable|integer|exists:skema,id',
            'start_date' => 'nullable|date|date_format:Y-m-d',
            'end_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:start_date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'skema_id.integer' => 'ID skema harus berupa angka.',
            'skema_id.exists' => 'Skema yang dipilih tidak ditemukan.',
            'start_date.date' => 'Tanggal mulai harus berupa tanggal yang valid.',
            'start_date.date_format' => 'Format tanggal mulai harus YYYY-MM-DD.',
            'end_date.date' => 'Tanggal akhir harus berupa tanggal yang valid.',
            'end_date.date_format' => 'Format tanggal akhir harus YYYY-MM-DD.',
            'end_date.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal mulai.',
        ];
    }

    /**
     * Get the validated data with parsed dates.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Parse dates to Carbon instances
        if (isset($validated['start_date'])) {
            $validated['start_date'] = Carbon::parse($validated['start_date']);
        }

        if (isset($validated['end_date'])) {
            $validated['end_date'] = Carbon::parse($validated['end_date']);
        }

        return $validated;
    }
}
