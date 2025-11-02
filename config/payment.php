<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nominal Pembayaran Asesor
    |--------------------------------------------------------------------------
    |
    | Nominal pembayaran per ujikom untuk asesor
    | Dapat diubah melalui file .env dengan key PAYMENT_ASESOR_PER_UJIKOM
    |
    */

    'asesor_per_ujikom' => env('PAYMENT_ASESOR_PER_UJIKOM', 500000),

    /*
    |--------------------------------------------------------------------------
    | Nominal Pendapatan TUK
    |--------------------------------------------------------------------------
    |
    | Nominal pendapatan per pendaftaran untuk TUK
    | Dapat diubah melalui file .env dengan key PAYMENT_TUK_PER_PENDAFTARAN
    |
    */

    'tuk_per_pendaftaran' => env('PAYMENT_TUK_PER_PENDAFTARAN', 1000000),

];
