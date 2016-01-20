<?php
namespace CbCaio\ImgAttacher\Contracts;

use Illuminate\Support\Collection;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

interface ImageProcessorContract
{
    /**
     * @param ImageManager $imageManager
     */
    public function __construct(ImageManager $imageManager);

    /**
     * @param AttacherImageContract $attacherImage
     * @return Image
     */
    public function createImageFromAttacherImage(AttacherImageContract $attacherImage);

    /**
     * @param AttacherImageContract $attacherImage
     * @param array                 $processing_styles
     * @return array
     */
    public function getProcessingStyleRoutine(AttacherImageContract $attacherImage, array $processing_styles);

    /**
     * @param AttacherImageContract $attacherImage
     * @param callable              $style
     * @return Image
     */
    public function applyStyle(AttacherImageContract $attacherImage, Callable $style);

    /**
     * @param AttacherImageContract $attacherImage
     * @param array                 $processing_styles
     * @return Collection |null
     */
    public function applyStyles(AttacherImageContract $attacherImage, $processing_styles);

}