<?php
namespace CbCaio\ImgAttacher\Processors;

use CbCaio\ImgAttacher\Contracts\AttacherImageContract;
use CbCaio\ImgAttacher\Contracts\ImageProcessorContract;
use Illuminate\Support\Collection;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

abstract class AbstractImageProcessor implements ImageProcessorContract
{
    /**
     * @var ImageManager
     */
    protected $imageManager;

    /**
     * @param ImageManager $imageManager
     */
    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    /**
     *  Creates an Intervention\Image from UploadedFile
     *
     * @return Image
     */
    public function createImageFromAttacherImage(AttacherImageContract $attacherImage)
    {
        return $this->imageManager->make($attacherImage->getUploadedFile());
    }

    /**
     * @param AttacherImageContract $attacherImage
     * @param array                 $processing_styles
     * @return Collection|null
     */
    public function applyStyles(AttacherImageContract $attacherImage, $processing_styles)
    {
        $style_routine        = $this->getProcessingStyleRoutine($attacherImage, $processing_styles);
        $styles_to_be_applied = empty($style_routine) ? head($processing_styles) : $style_routine;

        if (empty($styles_to_be_applied))
        {
            return null;
        }

        $collection_of_images = new Collection;

        foreach ($styles_to_be_applied as $style_name => $method)
        {
            $image = $this->applyStyle($attacherImage, $method);
            $collection_of_images->put($style_name,$image);
        }

        return $collection_of_images;
    }

    /**
     * @param AttacherImageContract $attacherImage
     * @param callable              $style
     * @return Image
     */
    public function applyStyle(AttacherImageContract $attacherImage, Callable $style)
    {
        $image = $this->createImageFromAttacherImage($attacherImage);

        $processed = $style($image);

        return (is_null($processed)) ? $image : $processed;
    }

    /**
     * @param AttacherImageContract $attacherImage
     * @param array                 $processing_styles
     * @return array
     */
    public function getProcessingStyleRoutine(AttacherImageContract $attacherImage, array $processing_styles)
    {
        return array_get($processing_styles, $attacherImage->getProcessingStyleRoutine(), []);
    }

}