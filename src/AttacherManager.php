<?php
namespace CbCaio\ImgAttacher;

use CbCaio\ImgAttacher\Contracts\AttacherImageContract;
use CbCaio\ImgAttacher\Managers\FileManager;
use CbCaio\ImgAttacher\Managers\FilePathManager;
use CbCaio\ImgAttacher\Processors\ImageProcessor;
use Intervention\Image\Image;

class AttacherManager
{
    protected $processing_styles_routine;
    protected $base_url;
    protected $path_to_save;

    public function __construct()
    {
        $this->processing_styles_routine = app('config')->get('img-attacher')['processing_styles_routines'];
    }

    /**
     * @return bool
     */
    public function writeImage(AttacherImageContract $attacherImage)
    {
        $imageProcessor = $this->getImageProcessor();

        $images_to_save = $imageProcessor->applyStyles($attacherImage, $this->getProcessingStylesRoutines());

        if (is_null($images_to_save))
        {
            return FALSE;
        }

        $needToUpdate = $attacherImage->hasDifferentFileName();

        if ($needToUpdate)
        {
            $this->getFileManager()->updateMany($images_to_save, $attacherImage);
        } else
        {
            $this->getFileManager()->saveMany($images_to_save, $attacherImage);
        }

        return TRUE;
    }

    /**
     * @param AttacherImageContract $attacherImage
     * @return bool
     */
    public function deleteImages(AttacherImageContract $attacherImage, $style = NULL)
    {
        $path = $attacherImage->getDeletePath($style);

        return $this->getFileManager()->delete($path);
    }

    /**
     * @return FileManager
     */
    public function getFileManager()
    {
        return app('img-attacher.FileManager');
    }

    /**
     * @return ImageProcessor
     */
    public function getImageProcessor()
    {
        return app('img-attacher.ImageProcessor');
    }

    /**
     * @return FilePathManager
     */
    public function getFilePathManager()
    {
        return app('img-attacher.FilePathManager');
    }

    /**
     * @return array
     */
    public function getProcessingStylesRoutines()
    {
        return $this->processing_styles_routine;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->getFilePathManager()->getBaseUrl();
    }

    /**
     * @return string
     */
    public function getPathToSave()
    {
        return $this->getFilePathManager()->getPath();
    }
}
