<?php

require dirname(__DIR__).'/vendor/autoload.php';
$app = require_once dirname(__DIR__).'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

$validator = Validator::make(
    ['password' => 'abc'],
    [
        'password' => ['required', Password::min(8)->mixedCase()->letters()->numbers()->symbols()]
    ]
);

print_r($validator->errors()->all());
