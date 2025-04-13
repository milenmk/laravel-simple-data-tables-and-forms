<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatables\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Milenmk\LaravelSimpleDatatables\Services\AssetService;
use Symfony\Component\Console\Command\Command as CommandAlias;

class PublishAssetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simple-datatables:publish-assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish assets for Laravel Simple Datatables';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Publishing Laravel Simple Datatables assets...');

        $vendorPath = 'vendor/milenmk/laravel-simple-datatables';
        $cssDestPath = public_path($vendorPath . '/css');
        $jsDestPath = public_path($vendorPath . '/js');

        // Create directories if they don't exist
        if (! File::exists($cssDestPath)) {
            File::makeDirectory($cssDestPath, 0755, true);
        }

        if (! File::exists($jsDestPath)) {
            File::makeDirectory($jsDestPath, 0755, true);
        }

        // Copy CSS file from package
        $packageCssPath = __DIR__ . '/../../public/css/package.css';
        if (File::exists($packageCssPath)) {
            File::copy($packageCssPath, $cssDestPath . '/package.css');
            $this->info('CSS file copied from package.');
        } else {
            $this->warn('No CSS file found in package. Creating an empty placeholder.');
            File::put($cssDestPath . '/package.css', '/* Laravel Simple Datatables CSS */');
        }

        // Copy JS file from package
        $packageJsPath = __DIR__ . '/../../public/js/package.js';
        if (File::exists($packageJsPath)) {
            File::copy($packageJsPath, $jsDestPath . '/package.js');
            $this->info('JS file copied from package.');
        } else {
            $this->warn('No JS file found in package. Creating an empty placeholder.');
            File::put($jsDestPath . '/package.js', '/* Laravel Simple Datatables JS */');
        }

        // Get asset service to handle minification
        $assetService = app(AssetService::class);

        // Get CSS and JS paths (this will also handle minification)
        $cssPath = $assetService->getCssPath();
        $jsPath = $assetService->getJsPath();

        $this->info('Assets published successfully:');
        $this->line('- CSS: ' . $cssPath);
        $this->line('- JS: ' . $jsPath);

        return CommandAlias::SUCCESS;
    }
}
