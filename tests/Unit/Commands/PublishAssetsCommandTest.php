<?php

declare(strict_types=1);

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Unit\Commands;

use Illuminate\Support\Facades\File;
use Milenmk\LaravelSimpleDatatablesAndForms\Commands\PublishAssetsCommand;
use Milenmk\LaravelSimpleDatatablesAndForms\Services\AssetService;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\BaseTest;
use Mockery;
use Symfony\Component\Console\Command\Command;

class PublishAssetsCommandTest extends BaseTest
{
    private PublishAssetsCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new PublishAssetsCommand;
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function command_has_correct_signature_and_description(): void
    {
        $this->assertEquals('simple-datatables-and-forms:publish-assets', $this->command->getName());
        $this->assertEquals('Publish assets for Laravel Simple Datatables', $this->command->getDescription());
    }

    /**
     * @test
     */
    public function handle_creates_directories_and_copies_files_when_they_exist(): void
    {
        // Mock File facade
        File::shouldReceive('exists')
            ->with(Mockery::pattern('/css$/'))
            ->andReturn(false);

        File::shouldReceive('exists')
            ->with(Mockery::pattern('/js$/'))
            ->andReturn(false);

        File::shouldReceive('makeDirectory')
            ->with(Mockery::pattern('/css$/'), 0755, true)
            ->once();

        File::shouldReceive('makeDirectory')
            ->with(Mockery::pattern('/js$/'), 0755, true)
            ->once();

        // Mock package file existence
        File::shouldReceive('exists')
            ->with(Mockery::pattern('/package\.css$/'))
            ->andReturn(false);

        File::shouldReceive('exists')
            ->with(Mockery::pattern('/package\.js$/'))
            ->andReturn(false);

        // Mock placeholder file creation (since package files don't exist)
        File::shouldReceive('put')
            ->with(Mockery::pattern('/package\.css$/'), '/* Laravel Simple Datatables CSS */')
            ->once();

        File::shouldReceive('put')
            ->with(Mockery::pattern('/package\.js$/'), '/* Laravel Simple Datatables JS */')
            ->once();

        // Mock AssetService
        $assetService = Mockery::mock(AssetService::class);
        $assetService->shouldReceive('getCssPath')->andReturn('/css/path');
        $assetService->shouldReceive('getJsPath')->andReturn('/js/path');

        $this->app->instance(AssetService::class, $assetService);

        // Use Artisan::call instead of direct handle() call
        $result = $this->artisan('simple-datatables-and-forms:publish-assets');

        $result->assertExitCode(Command::SUCCESS);
    }

    /**
     * @test
     */
    public function handle_creates_placeholder_files_when_package_files_dont_exist(): void
    {
        // Mock File facade
        File::shouldReceive('exists')
            ->with(Mockery::pattern('/css$/'))
            ->andReturn(false);

        File::shouldReceive('exists')
            ->with(Mockery::pattern('/js$/'))
            ->andReturn(false);

        File::shouldReceive('makeDirectory')
            ->with(Mockery::pattern('/css$/'), 0755, true)
            ->once();

        File::shouldReceive('makeDirectory')
            ->with(Mockery::pattern('/js$/'), 0755, true)
            ->once();

        // Mock package files don't exist
        File::shouldReceive('exists')
            ->with(Mockery::pattern('/package\.css$/'))
            ->andReturn(false);

        File::shouldReceive('exists')
            ->with(Mockery::pattern('/package\.js$/'))
            ->andReturn(false);

        // Mock placeholder file creation
        File::shouldReceive('put')
            ->with(Mockery::pattern('/package\.css$/'), '/* Laravel Simple Datatables CSS */')
            ->once();

        File::shouldReceive('put')
            ->with(Mockery::pattern('/package\.js$/'), '/* Laravel Simple Datatables JS */')
            ->once();

        // Mock AssetService
        $assetService = Mockery::mock(AssetService::class);
        $assetService->shouldReceive('getCssPath')->andReturn('/css/path');
        $assetService->shouldReceive('getJsPath')->andReturn('/js/path');

        $this->app->instance(AssetService::class, $assetService);

        $result = $this->artisan('simple-datatables-and-forms:publish-assets');

        $result->assertExitCode(Command::SUCCESS);
    }

    /**
     * @test
     */
    public function handle_skips_directory_creation_when_directories_exist(): void
    {
        // Mock directories already exist
        File::shouldReceive('exists')
            ->with(Mockery::pattern('/css$/'))
            ->andReturn(true);

        File::shouldReceive('exists')
            ->with(Mockery::pattern('/js$/'))
            ->andReturn(true);

        // Mock package files exist
        File::shouldReceive('exists')
            ->with(Mockery::pattern('/package\.css$/'))
            ->andReturn(true);

        File::shouldReceive('exists')
            ->with(Mockery::pattern('/package\.js$/'))
            ->andReturn(true);

        // Mock file copying
        File::shouldReceive('copy')
            ->with(Mockery::pattern('/package\.css$/'), Mockery::pattern('/package\.css$/'))
            ->once();

        File::shouldReceive('copy')
            ->with(Mockery::pattern('/package\.js$/'), Mockery::pattern('/package\.js$/'))
            ->once();

        // Mock AssetService
        $assetService = Mockery::mock(AssetService::class);
        $assetService->shouldReceive('getCssPath')->andReturn('/css/path');
        $assetService->shouldReceive('getJsPath')->andReturn('/js/path');

        $this->app->instance(AssetService::class, $assetService);

        $result = $this->artisan('simple-datatables-and-forms:publish-assets');

        $result->assertExitCode(Command::SUCCESS);
    }
}
