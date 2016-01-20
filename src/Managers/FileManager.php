<?php
namespace CbCaio\ImgAttacher\Managers;

use CbCaio\ImgAttacher\Contracts\AttacherImageContract;
use CbCaio\ImgAttacher\Contracts\FileManagerContract;
use GrahamCampbell\Flysystem\FlysystemManager;
use Illuminate\Support\Collection;
use Intervention\Image\Image;

class FileManager implements FileManagerContract
{
    /**
     * @var \GrahamCampbell\Flysystem\FlysystemManager
     */
    protected $flysystem;

    public function __construct(FlysystemManager $flysystemManager)
    {
        $this->flysystem = $flysystemManager;
    }

    /**
     * @param Image  $image
     * @param string $path
     * @return bool
     */
    public function save(Image $image, $path)
    {
        return $this->flysystem->put($path, $image->encode());
    }

    /**
     * @param Collection            $images
     * @param AttacherImageContract $attacherImage
     * @return bool
     */
    public function saveMany(Collection $images, AttacherImageContract $attacherImage)
    {
        foreach ($images as $style_name => $image)
        {
            $this->save($image, $attacherImage->getPath($style_name));
        }

        return TRUE;
    }

    /**
     * @param Image  $image
     * @param string $path
     * @param string $oldPath
     * @return bool
     */
    public function update(Image $image, $path, $oldPath)
    {
        return $this->delete($oldPath) ? $this->flysystem->put($path, $image->encode()) : FALSE;
    }

    /**
     * @param Collection            $images
     * @param AttacherImageContract $attacherImage
     * @return bool
     */
    public function updateMany(Collection $images, AttacherImageContract $attacherImage)
    {
        return $this->delete($attacherImage->getPreviousPath())
            ? $this->saveMany($images, $attacherImage)
            : FALSE;
    }

    /**
     * @param string $path
     * @return bool
     */
    public function delete($path)
    {
        $folders_list = $this->flysystem->listContents($path, FALSE);

        if (empty($folders_list))
        {
            return FALSE;
        } else
        {
            foreach ($this->flysystem->listContents($path, FALSE) as $folder)
            {
                if ($folder['type'] == 'dir')
                {
                    $this->flysystem->deleteDir($folder['path']);
                } else
                {
                    $this->flysystem->delete($folder['path']);
                }
            }

            return TRUE;
        }
    }

    /**
     * @param string $path
     * @return bool
     */
    public function pathExists($path)
    {
        return $this->flysystem->has($path);
    }
}
