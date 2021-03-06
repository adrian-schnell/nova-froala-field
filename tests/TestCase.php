<?php

namespace Froala\NovaFroalaField\Tests;

use Laravel\Nova\Nova;
use Illuminate\Support\Facades\Route;
use Laravel\Nova\NovaServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Laravel\Nova\NovaApplicationServiceProvider;
use Froala\NovaFroalaField\FroalaFieldServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Froala\NovaFroalaField\Tests\Fixtures\TestResource;

abstract class TestCase extends OrchestraTestCase
{
    const DISK = 'public';

    public function setUp()
    {
        parent::setUp();

        Route::middlewareGroup('nova', []);

        $this->setUpDatabase($this->app);

        Nova::resources([
            TestResource::class,
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            NovaServiceProvider::class,
            NovaApplicationServiceProvider::class,
            FroalaFieldServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * Set up the database.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        $this->artisan('migrate:fresh');

        include_once __DIR__.'/../database/migrations/create_froala_attachment_tables.php.stub';

        (new \CreateFroalaAttachmentTables())->up();

        $app['db']->connection()->getSchemaBuilder()->create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('content');
        });
    }
}
