<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GeneratePostgresSeeders extends Command
{
    protected $signature = 'make:pgseeders';
    protected $description = 'Generate seeders from existing PostgreSQL database and auto-register them';

    public function handle()
    {
        $tables = DB::select("
            SELECT tablename 
            FROM pg_tables 
            WHERE schemaname = 'public'
        ");

        $seederList = [];

        foreach ($tables as $table) {
            $className = $this->generateSeederForTable($table->tablename);
            if ($className) {
                $seederList[] = $className;
            }
        }

        if (!empty($seederList)) {
            $this->updateDatabaseSeeder($seederList);
            $this->info('✅ All PostgreSQL seeders generated and registered in DatabaseSeeder.php.');
        } else {
            $this->warn('⚠️ No seeders were generated.');
        }
    }

    private function generateSeederForTable(string $table)
    {
        $data = DB::table($table)->get();
        if ($data->isEmpty()) {
            $this->warn("⚠️ Table {$table} is empty. Skipped.");
            return null;
        }

        $className = Str::studly($table) . 'Seeder';
        $filePath = database_path("seeders/{$className}.php");

        $arrayData = var_export(json_decode(json_encode($data), true), true);

        $template = <<<PHP
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class {$className} extends Seeder
{
    public function run(): void
    {
        DB::table('{$table}')->insert({$arrayData});
    }
}
PHP;

        File::put($filePath, $template);
        $this->info("✅ Seeder created for table: {$table}");
        return $className;
    }

    private function updateDatabaseSeeder(array $seeders)
    {
        $databaseSeederPath = database_path('seeders/DatabaseSeeder.php');
        $content = File::get($databaseSeederPath);

        $content = preg_replace('/\$this->call\(\[.*\]\);/sU', '', $content);

        $callList = "        \$this->call([\n";
        foreach ($seeders as $seeder) {
            $callList .= "            {$seeder}::class,\n";
        }
        $callList .= "        ]);";

        $content = preg_replace(
            '/public function run\(\): void\s*\{\s*/',
            "public function run(): void\n    {\n" . $callList . "\n\n        ",
            $content
        );

        File::put($databaseSeederPath, $content);
    }
}
