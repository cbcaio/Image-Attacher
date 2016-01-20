<?php

namespace CbCaio\ImgAttacher\Testing;

use CbCaio\ImgAttacher\Managers\FileManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use CbCaio\ImgAttacher\Models\AttacherImage;

class PackageTest extends AbstractTestCase
{
    use DatabaseTransactions;

    /** @var UploadedFile $model */
    protected $uploaded_file;

    /** @var UploadedFile $model */
    protected $uploaded_file_2;

    /** @var User $model */
    protected $model;

    /** @var FileManager $model */
    protected $filemanager;

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
        $this->filemanager     = app('img-attacher.FileManager');
    }

    /**
     * Asserting if the User model has traits.
     */
    public function testUserShouldHasPermissionsTrait()
    {
        $this->assertUsingTrait(
            'CbCaio\ImgAttacher\Traits\HasImage',
            'CbCaio\ImgAttacher\Testing\User'
        );
    }


    /** @test */
    public function a_model_can_add_a_relationship_from_uploaded_image()
    {
        $this->model->addImage($this->uploaded_file);
        $this->assertTrue($this->model->hasImage());
        $this->assertTrue($this->model->getImage() instanceof AttacherImage);

        $this->model->deleteImage();
    }

    /** @test */
    public function a_model_can_delete_its_image_relationship()
    {
        $this->model->addImage($this->uploaded_file);
        $this->assertTrue($this->model->deleteImage());
        $this->assertFalse($this->model->hasImage());
    }

    /** @test */
    public function models_can_override_existing_image()
    {
        $this->model->addImage($this->uploaded_file);
        $image = $this->model->getImage();
        $this->assertEquals($image->getFileNameAttribute(), 'laravel.png');

        $this->model->addImage($this->uploaded_file_2);
        $image = $this->model->getImage();
        $this->assertEquals($image->getFileNameAttribute(), 'php.png');

        $this->model->deleteImage();
    }

    /** @test */
    public function all_styles_are_being_processed()
    {
        $this->model->addImage($this->uploaded_file);
        $image = $this->model->getImage();

        $this->assertEquals("/uploads/images/User/1/original_style/laravel.png", $image->getPath('original_style'));
        $this->assertEquals("/uploads/images/User/1/original_style2/laravel.png", $image->getPath('original_style2'));

        $this->model->deleteImage();

        $this->model->addImage($this->uploaded_file, 'default_routine2');

        $image = $this->model->getImage();

        $this->assertNotEquals("/uploads/images/User/1/original_style/laravel.png",
                               $image->getPath('original_style2'));

        $this->assertEquals("/uploads/images/User/1/:style/laravel.png",
                            $image->getPath('original_style222'));
        $this->assertEquals("/uploads/images/User/1/original_style3/laravel.png",
                            $image->getPath('original_style3'));
        $this->assertEquals("/uploads/images/User/1/original_style4/laravel.png",
                            $image->getPath('original_style4'));

        $this->model->deleteImage();
    }


    /** @test */
    public function image_file_is_created_when_relationship_is_added()
    {
        $this->model->addImage($this->uploaded_file);
        $image = $this->model->getImage();

        $wasWritten = $this->filemanager->pathExists($image->getPath('original_style'));
        $this->assertTrue($wasWritten);

        $wasWritten = $this->filemanager->pathExists($image->getPath('original_style2'));
        $this->assertTrue($wasWritten);

        $this->model->deleteImage();
    }

    public function image_file_overides_old_files_when_relationship_is_added_again()
    {
        $this->model->addImage($this->uploaded_file);
        $image = $this->model->getImage();

        $firstFilePath       = $image->getPath('original_style');
        $wasWrittenFirstTime = $this->filemanager->pathExists($firstFilePath);
        $this->assertTrue($wasWrittenFirstTime);

        $firstFilePathOtherStyle       = $image->getPath('original_style2');
        $wasWrittenFirstTimeOtherStyle = $this->filemanager->pathExists($firstFilePathOtherStyle);
        $this->assertTrue($wasWrittenFirstTimeOtherStyle);

        $this->model->addImage($this->uploaded_file_2);
        $image = $this->model->getImage();

        $secondFilePath           = $image->getPath('original_style');
        $secondFilePathOtherStyle = $image->getPath('original_style2');

        $wasWrittenSecondTime = $this->filemanager->pathExists($secondFilePath);

        $wasWrittenSecondTimeOtherStyle = $this->filemanager->pathExists($secondFilePathOtherStyle);
        $this->assertNotEquals($firstFilePath, $image->getPath('original_style'));
        $this->assertNotEquals($firstFilePathOtherStyle, $image->getPath('original_style2'));

        $this->assertTrue($wasWrittenSecondTime);
        $this->assertTrue($wasWrittenSecondTimeOtherStyle);

        $this->model->deleteImage();
    }
}
