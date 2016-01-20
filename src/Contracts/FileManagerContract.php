<?php
namespace CbCaio\ImgAttacher\Contracts;

use GrahamCampbell\Flysystem\FlysystemManager;
use Illuminate\Support\Collection;
use Intervention\Image\Image;

interface FileManagerContract
{
    /**
     * @param FlysystemManager $flysystemManager
     */
    public function __construct(FlysystemManager $flysystemManager);

    /**
     * @param Image  $image
     * @param string $path
     * @return bool
     */
    public function save(Image $image, $path);

    /**
     * @param Collection            $images
     * @param AttacherImageContract $attacherImage
     * @return bool
     */
    public function saveMany(Collection $images, AttacherImageContract $attacherImage);

    /**
     * @param Image  $image
     * @param string $path
     * @param string $oldPath
     * @return bool
     */
    public function update(Image $image, $path, $oldPath);

    /**
     * @param Collection            $images
     * @param AttacherImageContract $attacherImage
     * @return bool
     */
    public function updateMany(Collection $images, AttacherImageContract $attacherImage);

    /**
     * @param $path
     * @return bool
     */
    public function delete($path);

    /**
     * @param string $path
     * @return bool
     */
    public function pathExists($path);

}

