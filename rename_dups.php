<?php
$dir = __DIR__ . '/c:\Users\user\OneDrive\Documents\KULIAH\SMT 8\SKRIPSI\amanah-baiturrahman\database\migrations';
// fix path
$dir = 'c:\Users\user\OneDrive\Documents\KULIAH\SMT 8\SKRIPSI\amanah-baiturrahman\database\migrations';

$files = scandir($dir);
foreach ($files as $file) {
    if (strpos($file, '2026_03_13_') === 0 || strpos($file, '0001_01_01_') === 0 || strpos($file, '2026_04_27_') === 0) {
        rename($dir . '/' . $file, $dir . '/' . $file . '.bak');
        echo "Renamed $file\n";
    }
}
echo "Done.\n";
