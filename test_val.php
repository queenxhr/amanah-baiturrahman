<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

$validator = Validator::make(
    ['password' => 'abc'],
    [
        'password' => ['required', Password::min(8)->mixedCase()->letters()->numbers()->symbols()]
    ],
    [
        'password.required' => 'Kolom kata sandi wajib diisi.',
        'password.min' => 'Kolom kata sandi harus minimal 8 karakter.',
        'password.mixed' => 'Kolom kata sandi harus mengandung setidaknya satu huruf besar dan satu huruf kecil.',
        'password.letters' => 'Kolom kata sandi harus mengandung setidaknya satu huruf.',
        'password.numbers' => 'Kolom kata sandi harus mengandung setidaknya satu angka.',
        'password.symbols' => 'Kolom kata sandi harus mengandung setidaknya satu simbol.',
    ]
);

print_r($validator->errors()->all());
