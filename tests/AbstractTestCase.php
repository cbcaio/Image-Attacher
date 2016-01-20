<?php

namespace CbCaio\ImgAttacher\Testing;

use Orchestra\Testbench\TestCase;

abstract class AbstractTestCase extends TestCase
{
    /**
     * Array of service providers should be loaded before tests.
     * @var array
     */
    protected $providers = [
        \CbCaio\ImgAttacher\Providers\ImgAttacherServiceProvider::class]
    ;

    /**
     * Array of test case which should not load the service providers.
     * @var array
     */
    protected $skipProvidersFor = [];

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');

        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('auth.model', 'CbCaio\ImgAttacher\Testing\User');
    }

    /**
     * Performs migrations.
     * @param string|array $path string or array of paths to find migrations.
     */
    public function migrate($path = null)
    {
        $paths = is_array($path) ? $path : [$path];

        foreach ($paths as $path) {
            $code = $this->artisan(
                'migrate',
                ['--realpath' => $path]
            );

            $this->assertEquals(
                0,
                $code,
                sprintf(
                    'Something went wrong when migrating %s.',
                    str_replace(realpath($this->srcPath('..')), '', realpath($path))
                )
            );
        }
    }

    /**
     * Seed database.
     * @param string|array $seeder String or Array of classes to seed.
     */
    public function seed($seeder = 'UsersTableSeeder')
    {
        $seeders = is_array($seeder) ? $seeder : [$seeder];

        foreach ($seeders as $seeder) {
            $code = $this->artisan(
                'db:seed',
                ['--class' => str_contains($seeder, '\\') ? $seeder : 'CbCaio\ImgAttacher\Testing\\'.$seeder]
            );

            $this->assertEquals(0, $code, sprintf('Something went wrong when seeding %s.', $seeder));
        }
    }

    /**
     * Assert if the instance or classname uses a trait.
     * @param string $trait    Name of the trait (namespaced)
     * @param mixed  $instance Instance or name of the class
     */
    public function assertUsingTrait($trait, $instance)
    {
        $this->assertTrue(
            in_array($trait, class_uses_recursive($instance)),
            sprintf(
                'Fail to assert the class %s uses trait %s.',
                is_string($instance) ? $instance : get_class($instance),
                $trait
            )
        );
    }

    /**
     * Get source package path.
     *
     * @param string $path
     *
     * @return string
     */
    public function srcPath($path = null)
    {
        return __DIR__.'/../src'.$this->parseSubPath($path);
    }

    /**
     * Get the resources path.
     *
     * @param string $path
     *
     * @return string
     */
    public function resourcePath($path = null)
    {
        return $this->srcPath('/../resources').$this->parseSubPath($path);
    }

    /**
     * Stubs path.
     *
     * @param string $path
     *
     * @return string
     */
    public function stubsPath($path = null)
    {
        return __DIR__.'/stubs'.$this->parseSubPath($path);
    }

    /**
     * @param string $path
     * @return string
     */
    public function filesPath($path = null)
    {
        return __DIR__.'/stubs/files'.$this->parseSubPath($path);
    }

    /**
     * Get package providers.
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        if (in_array($this->getName(), $this->skipProvidersFor)) {
            return [];
        }

        return $this->providers;
    }

    /**
     * Trim slashes of path and return prefixed by DIRECTORY_SEPARATOR.
     * @param string $path
     * @return string
     */
    protected function parseSubPath($path)
    {
        return $path ? DIRECTORY_SEPARATOR.trim($path, DIRECTORY_SEPARATOR) : '';
    }
}