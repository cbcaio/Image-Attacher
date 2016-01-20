<?php
namespace CbCaio\ImgAttacher\Testing;

use CbCaio\ImgAttacher\Managers\FileManager;
use CbCaio\ImgAttacher\Models\AttacherImage;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HasImageTraitTest extends AbstractTestCase
{
    use DatabaseTransactions;

    /** @var UploadedFile $model */
    protected $uploaded_file;

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
        $this->model           = \CbCaio\ImgAttacher\Testing\User::query('id', '=', 1)->first();
    }

    /** @test */
    public function can_retrieve_image_morph_one_relationship()
    {
        $this->assertTrue($this->model->image() instanceof MorphOne);
    }

    /** @test */
    public function can_add_image()
    {
        $this->model->addImage($this->uploaded_file);
        $this->assertTrue($this->model->hasImage());
        $this->model->deleteImage();
    }

    /** @test */
    public function add_image_returns_attacher_image_if_image_added_successfully()
    {
        $this->model->addImage($this->uploaded_file);
        $this->assertTrue($this->model->getImage() instanceof AttacherImage);
        $this->model->deleteImage();
    }

    /** @test */
    public function can_delete_image()
    {
        $this->model->addImage($this->uploaded_file);
        $this->model->deleteImage();
        $this->assertFalse($this->model->hasImage());
    }

    /** @test */
    public function delete_image_returns_true_if_image_deleted_successfully()
    {
        $this->model->addImage($this->uploaded_file);
        $this->assertTrue($this->model->deleteImage());
    }

    /** @test */
    public function trying_to_delete_inexistent_image_returns_false()
    {
        $this->assertFalse($this->model->deleteImage());
    }

    /** @test */
    public function can_verify_if_has_image()
    {
        $this->assertFalse($this->model->hasImage());
        $this->model->addImage($this->uploaded_file);
        $this->assertTrue($this->model->hasImage());
        $this->model->deleteImage();
    }

    /** @test */
    public function related_instance_is_instance_of_attacher_image()
    {
        $this->assertTrue($this->model->image()->getRelated()->newInstance() instanceof AttacherImage);
    }
}
