<?php
namespace CbCaio\ImgAttacher\Testing;

use \CbCaio\ImgAttacher\Managers\FileManager;
use \CbCaio\ImgAttacher\Managers\FilePathManager;
use CbCaio\ImgAttacher\Processors\ImageProcessor;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AttacherManagerTest extends AbstractTestCase
{
    use DatabaseTransactions;

    /** @test */
    public function can_retrieve_file_manager()
    {
        $return = app('img-attacher')->getFileManager();

        $this->assertTrue($return instanceof FileManager);
    }

    /** @test */
    public function can_retrieve_file_path_manager()
    {
        $return = app('img-attacher')->getFilePathManager();

        $this->assertTrue($return instanceof FilePathManager);
    }

    /** @test */
    public function can_retrieve_image_processor()
    {
        $return = app('img-attacher')->getImageProcessor();

        $this->assertTrue($return instanceof ImageProcessor);
    }

    /** @test */
    public function can_retrieve_processing_styles_from_config_file()
    {
        $string = app('img-attacher')->getProcessingStylesRoutines();

        $this->assertEquals($string, app('config')->get('img-attacher')['processing_styles_routines']);
    }

    /** @test */
    public function can_retrieve_base_url_from_config_file()
    {
        $string = app('img-attacher')->getBaseUrl();

        $this->assertEquals($string, app('config')->get('img-attacher')['base_url']);
    }

    /** @test */
    public function can_retrieve_path_to_save_from_config_file()
    {
        $string = app('img-attacher')->getPathToSave();

        $this->assertEquals($string, app('config')->get('img-attacher')['path_to_save']);
    }

}
