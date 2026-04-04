<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeModuleFull extends Command
{
    protected $signature = 'make:module-full {name}';
    protected $description = 'Create a full independent Laravel module with folders, routes, ServiceProvider and composer.json skeleton';

    protected Filesystem $files;

    public function __construct()
    {
        parent::__construct();
        $this->files = new Filesystem();
    }

    public function handle()
    {
        $name = $this->argument('name');
        $base = base_path("Modules/$name");

        // قائمة الفولدرات
        $folders = [
            "$base/app/Http/Controllers/Api",
            "$base/app/Http/Controllers/Admin",
            "$base/app/Http/Requests/Api",
            "$base/app/Http/Requests/Admin",
            "$base/app/Http/Middleware",
            "$base/app/Models",
            "$base/app/Services/Api",
            "$base/app/Services/Admin",
            "$base/config",
            "$base/database/migrations",
            "$base/database/seeders",
            "$base/database/factories",
            "$base/routes",
            "$base/Providers",
            "$base/tests/Feature",
            "$base/tests/Unit",
        ];

        foreach ($folders as $folder) {
            if (!$this->files->isDirectory($folder)) {
                $this->files->makeDirectory($folder, 0755, true);
                $this->info("Created folder: $folder");
            }
        }

        // ServiceProvider فارغ
        $providerPath = "$base/Providers/{$name}ServiceProvider.php";
        $providerNamespace = "Modules\\$name\\Providers";
        $providerContent = <<<PHP
<?php

namespace $providerNamespace;

use Illuminate\Support\ServiceProvider;

class {$name}ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \$this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
        \$this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        \$this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }
}
PHP;
        $this->files->put($providerPath, $providerContent);
        $this->info("Created ServiceProvider: $providerPath");

        // Routes فارغة
        $this->files->put("$base/routes/api.php", "<?php\n\n// API routes for $name module\n");
        $this->files->put("$base/routes/web.php", "<?php\n\n// Web routes for $name module\n");

        // composer.json skeleton للموديول
        $composerSkeleton = [
            "name" => "modules/$name",
            "description" => "$name module",
            "autoload" => [
                "psr-4" => [
                    "Modules\\$name\\" => "app/"
                ]
            ],
            "extra" => [
                "laravel" => [
                    "providers" => [
                        "Modules\\$name\\Providers\\{$name}ServiceProvider"
                    ]
                ]
            ]
        ];
        $this->files->put("$base/composer.json", json_encode($composerSkeleton, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
        $this->info("Created composer.json skeleton: $base/composer.json");

        $this->info("Module '$name' created successfully!");
    }
}