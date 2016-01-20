<?php
namespace CbCaio\ImgAttacher\Testing;

use GrahamCampbell\Flysystem\FlysystemManager;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use CbCaio\ImgAttacher\Managers\FilePathManager;
use CbCaio\ImgAttacher\Models\AttacherImage;

class FilePathManagerTest extends AbstractTestCase
{
    use DatabaseTransactions;

    /** @var UploadedFile $model */
    protected $uploaded_file;

    /** @var UploadedFile $model */
    protected $uploaded_file_2;

    /** @var User $model */
    protected $model;

    /** @var FilePathManager $model */
    protected $filepathmanager;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->migrate([
                           $this->stubsPath('database/migrations'),
                           $this->resourcePath('migrations'),
                       ]);

        $this->seed([
                        'UserTableSeeder'
                    ]);

        $this->uploaded_file   = new UploadedFile($this->filesPath('laravel.png'), 'laravel.png', 'image/png');
        $this->uploaded_file_2 = new UploadedFile($this->filesPath('php.png'), 'php.png', 'image/png');
        $this->model           = \CbCaio\ImgAttacher\Testing\User::query('id', '=', 1)->first();
        $this->filepathmanager = app('img-attacher.FilePathManager');
    }

    /** @test */
    public function constructor_is_correclty_created_with_configs_from_file()
    {
        $config                    = config('img-attacher');
        $path_to_save_default      = $config['path_to_save'];
        $base_url                  = $config['base_url'];
        $processing_styles_routine = $config['processing_styles_routines'];

        $this->assertEquals($path_to_save_default, $this->filepathmanager->getPath());
        $this->assertEquals($base_url, $this->filepathmanager->getBaseUrl());
        $this->assertEquals($processing_styles_routine, $this->filepathmanager->getProcessingStylesRoutines());
    }

    /** @test */
    public function parse_path_with_valid_style_specified()
    {
        $this->model->addImage($this->uploaded_file);
        $image = $this->model->getImage();

        $expected_path = '/uploads/images/User/1/original_style/laravel.png';
        $this->assertEquals($expected_path, $this->filepathmanager->parsePath($image, 'original_style'));
    }

    /** @test */
    public function parse_path_with_valid_style_specified_and_file_name_specified()
    {
        $this->model->addImage($this->uploaded_file, NULL, 'file.png');
        $image = $this->model->getImage();

        $expected_path = '/uploads/images/User/1/original_style/file.png';
        $this->assertEquals($expected_path, $this->filepathmanager->parsePath($image, 'original_style'));
        $expected_path = '/uploads/images/User/1/original_style2/file.png';
        $this->assertEquals($expected_path, $this->filepathmanager->parsePath($image, 'original_style2'));
        $expected_path = '/uploads/images/User/1/original_style3/file.png';
        $this->assertEquals($expected_path, $this->filepathmanager->parsePath($image, 'original_style3'));
        $expected_path = '/uploads/images/User/1/:style/file.png';
        $this->assertEquals($expected_path, $this->filepathmanager->parsePath($image, 'or1igin1al_style3'));
    }

    /** @test */
    public function parse_path_with_invalid_style_specified()
    {
        $this->model->addImage($this->uploaded_file);
        $image = $this->model->getImage();

        $expected_path = '/uploads/images/User/1/:style/laravel.png';
        $this->assertEquals($expected_path, $this->filepathmanager->parsePath($image, '2original_style3'));

        $expected_path = '/uploads/images/User/1/:style/laravel.png';
        $this->assertEquals($expected_path, $this->filepathmanager->parsePath($image, 'foo-bar'));
    }

    /** @test */
    public function parse_path_with_valid_style_specified_and_different_than_default()
    {
        $this->model->addImage($this->uploaded_file, 'default_routine2');
        $image = $this->model->getImage();

        $expected_path = '/uploads/images/User/1/original_style3/laravel.png';
        $this->assertEquals($expected_path, $this->filepathmanager->parsePath($image, 'original_style3'));

        $expected_path = '/uploads/images/User/1/original_style4/laravel.png';
        $this->assertEquals($expected_path, $this->filepathmanager->parsePath($image, 'original_style4'));

        $expected_path = '/uploads/images/User/1/original_style/laravel.png';
        $this->assertEquals($expected_path, $this->filepathmanager->parsePath($image, 'original_style'));
    }

    /** @test */
    public function parse_previous_path_returns_expected_path()
    {
        $this->model->addImage($this->uploaded_file);
        $image = $this->model->getImage();

        // expect null because there is no previously saved image
        $this->assertNull($this->filepathmanager->parsePreviousPath($image));

        $image->setAttributesFromFile($this->uploaded_file_2);
        $this->assertEquals('/uploads/images/User/1', $this->filepathmanager->parsePreviousPath($image));
    }

    /** @test */
    public function parse_delete_path_returns_correct_path()
    {
        $this->model->addImage($this->uploaded_file);
        $image = $this->model->getImage();

        $this->assertEquals('/uploads/images/User/1', $this->filepathmanager->parseDeletePath($image));

        // the same with different routine specified
        $this->model->addImage($this->uploaded_file, 'default_routine2', 'foo.png');
        $image = $this->model->getImage();

        $this->assertEquals('/uploads/images/User/1', $this->filepathmanager->parseDeletePath($image));
    }

    /** @test */
    public function parse_delete_path_returns_correct_path_to_style()
    {
        $this->model->addImage($this->uploaded_file);
        $image = $this->model->getImage();

        // with valid style equals the default
        $this->assertEquals('/uploads/images/User/1/original_style',
                            $this->filepathmanager->parseDeletePath($image, 'original_style'));

        $this->assertEquals('/uploads/images/User/1/original_style2',
                            $this->filepathmanager->parseDeletePath($image, 'original_style2'));

        $this->assertEquals('/uploads/images/User/1/original_style3',
                            $this->filepathmanager->parseDeletePath($image, 'original_style3'));

        // with invalid style
        $this->assertEquals('/uploads/images/User/1/:style',
                            $this->filepathmanager->parseDeletePath($image, 'foo_bar'));

        // the same with different routine specified
        $this->model->addImage($this->uploaded_file, 'default_routine2', 'foo.png');
        $image = $this->model->getImage();

        // with valid style
        $this->assertEquals('/uploads/images/User/1/original_style',
                            $this->filepathmanager->parseDeletePath($image, 'original_style'));

        $this->assertEquals('/uploads/images/User/1/original_style2',
                            $this->filepathmanager->parseDeletePath($image, 'original_style2'));

        $this->assertEquals('/uploads/images/User/1/original_style3',
                            $this->filepathmanager->parseDeletePath($image, 'original_style3'));

        // with invalid style
        $this->assertEquals('/uploads/images/User/1/:style',
                            $this->filepathmanager->parseDeletePath($image, 'foo_bar'));
    }

    /** @test */
    public function parse_url_returns_correct_path_to_file()
    {
        $this->model->addImage($this->uploaded_file);
        $image = $this->model->getImage();

        // default
        $this->assertEquals('http://localhost/files/uploads/images/User/1/original_style/laravel.png',
                            $this->filepathmanager->parseUrl($image, 'original_style'));

        // with valid style equals the default
        $this->assertEquals('http://localhost/files/uploads/images/User/1/original_style/laravel.png',
                            $this->filepathmanager->parseUrl($image, 'original_style'));
        // with invalid style
        $this->assertEquals('http://localhost/files/uploads/images/User/1/:style/laravel.png',
                            $this->filepathmanager->parseUrl($image, 'foo_bar'));

        // with valid style different than default
        $this->assertEquals('http://localhost/files/uploads/images/User/1/original_style2/laravel.png',
                            $this->filepathmanager->parseUrl($image, 'original_style2'));


        $this->model->addImage($this->uploaded_file, NULL, 'foo.png');
        $image = $this->model->getImage();

        $this->assertEquals('foo.png', $image->getFileNameAttribute());

        // the same with defined filename

        // default
        $this->assertEquals('http://localhost/files/uploads/images/User/1/original_style/foo.png',
                            $this->filepathmanager->parseUrl($image, 'original_style'));

        // with valid style equals the default
        $this->assertEquals('http://localhost/files/uploads/images/User/1/original_style/foo.png',
                            $this->filepathmanager->parseUrl($image, 'original_style'));
        // with invalid style
        $this->assertEquals('http://localhost/files/uploads/images/User/1/:style/foo.png',
                            $this->filepathmanager->parseUrl($image, 'foo_bar'));

        // with valid style different than default
        $this->assertEquals('http://localhost/files/uploads/images/User/1/original_style2/foo.png',
                            $this->filepathmanager->parseUrl($image, 'original_style2'));
    }

    /** @test */
    public function parse_attributes_set_attributes_defined_in_arguments_array()
    {
        $this->filepathmanager->setArguments(
            [
                ':id'             => 'id',
                ':filename'       => 'file_name',
                ':file_extension' => 'file_extension',
                ':file_size'      => 'file_size',
                ':created'        => 'created_at',
                ':updated'        => 'updated_at'
            ]
        );

        $this->model->addImage($this->uploaded_file);
        $image = $this->model->getImage();

        $this->assertEquals("foo/bar/1/bar-again/laravel.png",
                            $this->filepathmanager->parseAttributes($image, 'foo/bar/:id/bar-again/:filename'));

        $this->assertEquals("png/154178/foo-bar/laravel.png",
                            $this->filepathmanager->parseAttributes($image,
                                                                    ':file_extension/:file_size/foo-bar/:filename'));
    }

    /** @test */
    public function parse_style_can_set_style_parameter_from_string()
    {
        // existing style
        $this->assertEquals("/foo/bar/original_style2/foobar", $this->filepathmanager->parseStyle(
            'original_style2',
            '/foo/bar/:style/foobar')
        );
        $this->assertEquals("/foo/bar/original_style/",
                            $this->filepathmanager->parseStyle('original_style', '/foo/bar/:style/'));
        $this->assertNotEquals("/foo/bar/original_style", $this->filepathmanager->parseStyle('original_style',
                                                                                             '/foo/bar/:style/'));

        // invalid style
        $this->assertEquals("/foo/bar/:style/foobar",
                            $this->filepathmanager->parseStyle('foo-bar', '/foo/bar/:style/foobar'));
        $this->assertEquals("/foo/bar/:style", $this->filepathmanager->parseStyle('test', '/foo/bar/:style'));
    }

    /** @test */
    public function parse_class_can_set_parameters_based_on_owner_model()
    {
        $this->model->addImage($this->uploaded_file);
        $image = $this->model->getImage();

        $this->assertEquals('/User/1/hadouken',
                            $this->filepathmanager->parseOwnerClass($image,'/:owner_class/:owner_id/hadouken'));
        $this->assertEquals('/1/User/hadouken',
                            $this->filepathmanager->parseOwnerClass($image,'/:owner_id/:owner_class/hadouken'));
        $this->assertEquals('User/1/hadouken',
                            $this->filepathmanager->parseOwnerClass($image, ':owner_class/:owner_id/hadouken'));
        $this->assertEquals('1/User/hadouken',
                            $this->filepathmanager->parseOwnerClass($image, ':owner_id/:owner_class/hadouken'));
        $this->assertEquals('/hadouken/User/1',
                            $this->filepathmanager->parseOwnerClass($image, '/hadouken/:owner_class/:owner_id'));
        $this->assertEquals('/hadouken/1/User',
                            $this->filepathmanager->parseOwnerClass($image, '/hadouken/:owner_id/:owner_class'));
        $this->assertEquals('/hadouken/User/1',
                            $this->filepathmanager->parseOwnerClass($image, '/hadouken/:owner_class/:owner_id'));
        $this->assertEquals('/hadouken/1/User',
                            $this->filepathmanager->parseOwnerClass($image, '/hadouken/:owner_id/:owner_class'));
    }

}
