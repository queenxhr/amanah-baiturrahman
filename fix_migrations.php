<?php
$dir = __DIR__ . '/database/migrations';

// 1. Restore 0001_* migrations
$files = scandir($dir);
foreach ($files as $file) {
    if (strpos($file, '0001_01_01_') === 0 && strpos($file, '.bak') !== false) {
        rename($dir . '/' . $file, $dir . '/' . str_replace('.bak', '', $file));
    }
}

// 2. Identify and backup duplicates in 2026_04_28_* and 2026_04_27_* that clash
$duplicates = [
    '2026_04_28_073049_create_users_table.php',
    '2026_04_28_073049_create_cache_table.php',
    '2026_04_28_073049_create_jobs_table.php',
    '2026_04_28_073049_create_failed_jobs_table.php',
    '2026_04_28_073049_create_job_batches_table.php',
    '2026_04_28_073049_create_password_reset_tokens_table.php',
    '2026_04_28_073049_create_sessions_table.php',
    '2026_04_27_040149_create_t02_users_table.php' // duplicate with 2026_04_28_073049_create_t02_users_table.php
];

foreach ($duplicates as $dup) {
    if (file_exists($dir . '/' . $dup)) {
        rename($dir . '/' . $dup, $dir . '/' . $dup . '.bak');
    }
}

echo "Migrations fixed.\n";
