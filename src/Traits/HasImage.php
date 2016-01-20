<?php
namespace CbCaio\ImgAttacher\Traits;

use CbCaio\ImgAttacher\Models\AttacherImage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait HasImage
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function image()
    {
        return $this->morphOne(config('img-attacher.model'), 'owner');
    }

    /**
     * @param UploadedFile $imageFile
     * @param string       $processing_style_routine
     * @param string       $filename
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function addImage(UploadedFile $imageFile, $processing_style_routine = NULL, $filename = NULL)
    {
        $this->hasImage()
            ? $attacherImage = $this->getImage()
            : $attacherImage = $this->createAttacherImageModel();

        $attacherImage->setAttributesFromFile($imageFile, $filename);
        $attacherImage->setProcessingStyleRoutine($processing_style_routine);

        return $this->image()->save($attacherImage);
    }

    /**
     * @return bool
     */
    public function deleteImage()
    {
        if ($this->hasImage())
        {
            $this->getImage()->delete();

            return TRUE;
        } else
        {
            return FALSE;
        }
    }

    /**
     * @return AttacherImage
     */
    public function getImage()
    {
        $image = $this->image()->getResults();

        return empty($image) ? NULL : $image;
    }

    /**
     * @return bool
     */
    public function hasImage()
    {
        return !is_null($this->getImage());
    }

    /**
     * @return AttacherImage
     */
    protected function createAttacherImageModel()
    {
        return $this->image()->getRelated()->newInstance();
    }
}