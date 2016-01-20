<?php
namespace CbCaio\ImgAttacher\Models;

use CbCaio\ImgAttacher\Contracts\AttacherImageContract;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class AbstractAttacherImage extends Model implements AttacherImageContract
{
    /**
     * @var string
     */
    protected $processing_style_routine;

    /**
     * @var UploadedFile
     */
    protected $uploaded_file;

    /**
     * @var string
     */
    protected $different_filename;

    /**
     * @param UploadedFile $file
     * @param string       $filename
     * @return $this
     */
    public function setAttributesFromFile(UploadedFile $file, $filename = NULL)
    {
        $this->setUploadedFile($file);

        is_null($filename)
            ? $this->setFileNameAttribute($file->getClientOriginalName())
            : $this->setFileNameAttribute($filename);


        $this->setFileExtensionAttribute($file->getClientOriginalExtension());
        $this->setMimeTypeAttribute($file->getClientMimeType());
        $this->setFileSizeAttribute($file->getSize());

        if ($this->filenameIsDifferentFromOriginal())
        {
            $this->setDifferentFilename();
        }

        return $this;
    }

    /**
     * @return mixed owner model
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /**
     * @return string
     */
    public function getOwnerType()
    {
        return array_has($this->attributes, 'owner_type')
            ? $this->attributes['owner_type']
            : NULL;
    }

    /**
     * Return the base_url followed by the path to the image related to the $processing_style after processing
     *
     * @param string|null $processing_style
     * @return string
     */
    public function getUrl($processing_style)
    {
        return app('img-attacher.FilePathManager')->parseUrl($this, $processing_style);
    }

    /**
     * Return the the path to the image related to the $processing_style after processing
     *
     * @param string|null $processing_style
     * @return string
     */
    public function getPath($processing_style)
    {
        return app('img-attacher.FilePathManager')->parsePath($this, $processing_style);
    }

    /**
     * @param null|string $processing_style
     * @return string
     */
    public function getDeletePath($processing_style = NULL)
    {
        return app('img-attacher.FilePathManager')->parseDeletePath($this, $processing_style);
    }

    /**
     * @return string
     */
    public function getPreviousPath()
    {
        return app('img-attacher.FilePathManager')->parsePreviousPath($this);
    }

    /**
     * @return string
     */
    public function getDifferentFilename()
    {
        return $this->different_filename;
    }

    /**
     * @return $this
     */
    public function setDifferentFilename()
    {
        $this->different_filename = $this->original['file_name'];

        return $this;
    }

    /**
     * @return bool
     */
    public function hasDifferentFileName()
    {
        return empty($this->different_filename) ? FALSE : TRUE;
    }

    /**
     * @return bool | null
     */
    public function filenameIsDifferentFromOriginal()
    {
        if (empty($this->original))
        {
            return NULL;
        }

        return ($this->attributes['file_name'] != $this->original['file_name']) ? TRUE : FALSE;
    }

    /**
     * @return string
     */
    public function getProcessingStyleRoutine()
    {
        return $this->processing_style_routine;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setProcessingStyleRoutine($name = NULL)
    {
        $this->processing_style_routine = isset($name) ? $name : 'default_routine';

        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getUploadedFile()
    {
        return $this->uploaded_file;
    }

    /**
     * @return UploadedFile
     */
    public function setUploadedFile(UploadedFile $file)
    {
        $this->uploaded_file = $file;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileNameAttribute()
    {
        return array_has($this->attributes, 'file_name')
            ? $this->attributes['file_name']
            : NULL;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setFileNameAttribute($name)
    {
        $file_name = str_slug(pathinfo($name, PATHINFO_FILENAME)) . '.' . pathinfo($name, PATHINFO_EXTENSION);

        $this->attributes['file_name'] = $file_name;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileExtensionAttribute()
    {
        return array_has($this->attributes, 'file_extension')
            ? $this->attributes['file_extension']
            : NULL;
    }

    /**
     * @param string $extension
     * @return $this
     */
    public function setFileExtensionAttribute($extension)
    {
        $this->attributes['file_extension'] = $extension;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileSizeAttribute()
    {
        return array_has($this->attributes, 'file_size')
            ? $this->attributes['file_size']
            : NULL;
    }

    /**
     * @param string $size
     * @return $this
     */
    public function setFileSizeAttribute($size)
    {
        $this->attributes['file_size'] = $size;

        return $this;
    }

    /**
     * @return string
     */
    public function getMimeTypeAttribute()
    {
        return array_has($this->attributes, 'mime_type')
            ? $this->attributes['mime_type']
            : NULL;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setMimeTypeAttribute($type)
    {
        $this->attributes['mime_type'] = $type;

        return $this;
    }

}