<?php

namespace CbCaio\ImgAttacher\Providers;

use CbCaio\ImgAttacher\Managers\FilePathManager;
use CbCaio\ImgAttacher\Models\AttacherImage;
use Illuminate\Support\ServiceProvider;

class ImgAttacherServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = FALSE;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        //$migration_timestamp = \Carbon\Carbon::now()->format('Y_m_d') . '_000000_';
        $migration_file_name = '2016_01_12_000000_create_attacher_images_table.php';

        $this->publishes(
            [
                __DIR__ . '/../../resources/config/img-attacher.php'
                => config_path('img-attacher.php'),
                __DIR__ . '/../../resources/database/migrations/' . $migration_file_name
                => database_path('migrations/'. $migration_file_name),
            ]
        );

        $this->mergeConfigFrom(
            __DIR__ . '/../../resources/config/img-attacher.php', 'img-attacher'
        );

        $this->registerEvents();
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();
        $this->registerDependencies();
        $this->registerAttacherManager();
        $this->registerFilePathManager();
        $this->registerFileManager();
        $this->registerImageProcessor();
    }

    private function registerEvents()
    {
        AttacherImage::saving(function ($model) {
            if ( !  app('img-attacher')->writeImage($model)) {
                return false;
            }
            return true;
        });

        AttacherImage::deleting(function ($model) {
            if ( !  app('img-attacher')->deleteImages($model)) {
                return false;
            }
            return true;
        });

    }

    private function registerAttacherManager()
    {
        $this->app->singleton('img-attacher', 'CbCaio\ImgAttacher\AttacherManager');
    }


    private function registerBindings()
    {
        $this->app->bind('CbCaio\ImgAttacher\Contracts\AttacherImageContract',
                         'CbCaio\ImgAttacher\Models\AttacherImage');
        $this->app->bind('CbCaio\ImgAttacher\Contracts\FilePathManagerContract',
                         'CbCaio\ImgAttacher\Managers\FilePathManager');
        $this->app->bind('CbCaio\ImgAttacher\Contracts\FileManagerContract',
                         'CbCaio\ImgAttacher\Managers\FileManager');
    }

    private function registerFileManager()
    {
        $this->app->singleton('img-attacher.FileManager', 'CbCaio\ImgAttacher\Managers\FileManager');
    }

    private function registerFilePathManager()
    {
        $this->app->singleton('img-attacher.FilePathManager', function ()
        {
            $config = config('img-attacher');

            return new FilePathManager($config['path_to_save'],$config['base_url'], $config['processing_styles_routines']);
        });
    }

    private function registerImageProcessor()
    {
        $this->app->singleton('img-attacher.ImageProcessor','CbCaio\ImgAttacher\Processors\ImageProcessor' );
    }

    private function registerDependencies()
    {
        $this->app->register(\GrahamCampbell\Flysystem\FlysystemServiceProvider::class);
        $this->app->register(\Intervention\Image\ImageServiceProvider::class);
    }

}