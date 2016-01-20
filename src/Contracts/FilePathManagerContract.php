<?php
namespace CbCaio\ImgAttacher\Contracts;

interface FilePathManagerContract
{
    /**
     * @param string $path
     * @param string $base_url
     * @param array  $processing_style
     */
    public function __construct($path, $base_url, $processing_style);

    /**
     * @return array
     */
    public function getArguments();

    /**
     * @param array $string
     * @return $this
     */
    public function setArguments($string);

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return string
     */
    public function getBaseUrl();

    /**
     * @param AttacherImageContract $attacherImage
     * @param string                $style
     * @return string|null
     */
    public function parsePath(AttacherImageContract $attacherImage, $style);

    /**
     * @param AttacherImageContract $attacherImage
     * @param string|null           $style
     * @return string
     */
    public function parseDeletePath(AttacherImageContract $attacherImage, $style = NULL);

    /**
     * @param AttacherImageContract $attacherImage
     * @param string                $style
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function parseUrl(AttacherImageContract $attacherImage, $style);

    /**
     * @param AttacherImageContract $attacherImage
     * @return string|null
     */
    public function parsePreviousPath(AttacherImageContract $attacherImage);

    /**
     * @param AttacherImageContract $attacherImage
     * @return string
     */
    public function parseAttributes(AttacherImageContract $attacherImage, $string);

    /**
     * @param string $style
     * @param string $string
     * @return string
     */
    public function parseStyle($style, $string);

    /**
     * @param AttacherImageContract $attacherImage
     * @param string                $string
     * @return string
     */
    public function parseOwnerClass(AttacherImageContract $attacherImage, $string);

    /**
     * @return array
     */
    public function getProcessingStylesRoutines();

}