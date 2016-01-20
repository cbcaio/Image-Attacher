<?php
namespace CbCaio\ImgAttacher\Contracts;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface AttacherImageContract
{
    /**
     * @param UploadedFile $file
     * @param string         $filename
     * @return $this
     */
    public function setAttributesFromFile(UploadedFile $file, $filename = NULL);

    /**
     * @return mixed
     */
        public function owner();

    /**
     * @return string
     */
    public function getOwnerType();

    /**
     * Return the base_url followed by the path to the image related to the $processing_style after processing
     *
     * @param string $processing_style
     * @return string
     */
    public function getUrl($processing_style);

    /**
     * Return the the path to the image related to the $processing_style after processing
     *
     * @param string $processing_style
     * @return string
     */
    public function getPath($processing_style);

    /**
     * Return the the path to root folder which the images related to the owner model were saved
     *
     * @param string $processing_style
     * @return string
     */
    public function getDeletePath($processing_style = NULL);

    /**
     * Return the the path to the previous saved image related, return is only valid while saving new instance
     *
     * @return string
     */
    public function getPreviousPath();

    /**
     * @return bool
     */
    public function filenameIsDifferentFromOriginal();

    /**
     * @return string
     */
    public function getProcessingStyleRoutine();

    /**
     * @param string $name
     * @return $this
     */
    public function setProcessingStyleRoutine($name);

    /**
     * @return UploadedFile $file
     */
    public function getUploadedFile();

    /**
     * @param UploadedFile $file
     * @return $this
     */
    public function setUploadedFile(UploadedFile $file);

    /**
     * @return string
     */
    public function getDifferentFilename();

    /**
     * @return $this
     */
    public function setDifferentFilename();

    /**
     * @return bool
     */
    public function hasDifferentFileName();

    /**
     * @return string
     */
    public function getFileNameAttribute();

    /**
     * @param string $name
     * @return $this
     */
    public function setFileNameAttribute($name);

    /**
     * @return string
     */
    public function getFileExtensionAttribute();

    /**
     * @param string $extension
     * @return $this
     */
    public function setFileExtensionAttribute($extension);

    /**
     * @return string
     */
    public function getFileSizeAttribute();

    /**
     * @param string $size
     * @return $this
     */
    public function setFileSizeAttribute($size);

    /**
     * @return string
     */
    public function getMimeTypeAttribute();

    /**
     * @param string $type
     * @return $this
     */
    public function setMimeTypeAttribute($type);

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getAttribute($key);

}