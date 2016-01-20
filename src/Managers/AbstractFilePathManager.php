<?php
namespace CbCaio\ImgAttacher\Managers;

use CbCaio\ImgAttacher\Contracts\AttacherImageContract;
use CbCaio\ImgAttacher\Contracts\FilePathManagerContract;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractFilePathManager implements FilePathManagerContract
{
    /** @var string */
    protected $path;

    /** @var string */
    protected $base_url;

    /** @var array */
    protected $processing_styles_routines;

    /** @var array */
    protected $arguments = [
        ':id'       => 'id',
        ':filename' => 'file_name'
    ];

    /**
     * @param string $path
     * @param string $base_url
     * @param array  $processing_styles_routines
     */
    public function __construct($path, $base_url, $processing_styles_routines)
    {
        $this->path                       = $path;
        $this->base_url                   = $base_url;
        $this->processing_styles_routines = $processing_styles_routines;
    }

    /**
     * @param AttacherImageContract $attacherImage
     * @param string                $style
     * @return mixed|string
     */
    public function parsePath(AttacherImageContract $attacherImage, $style)
    {
        $string = $this->parseStyle($style, $this->getPath());
        $string = $this->parseOwnerClass($attacherImage, $string);
        $string = $this->parseAttributes($attacherImage, $string);

        return $string;
    }

    /**
     * @param AttacherImageContract $attacherImage
     * @param string                $string
     * @return mixed
     */
    public function parseOwnerClass(AttacherImageContract $attacherImage, $string)
    {
        if (!empty($attacherImage->getOwnerType()))
        {
            $owner_class_name = last(preg_split('/\\\\/', $attacherImage->getOwnerType()));
            $string           = preg_replace('/:owner_class\b/', $owner_class_name, $string);
        } else
        {
            return $string;
        }

        $model_owner = $attacherImage->owner()->getResults();

        if ($model_owner instanceof Model)
        {
            $string = preg_replace('/:owner_id\b/', $model_owner->id, $string);
        }

        return $string;
    }

    /**
     * @param AttacherImageContract $attacherImage
     * @param string                $string
     * @return mixed
     */
    public function parseAttributes(AttacherImageContract $attacherImage, $string)
    {
        foreach ($this->getArguments() as $key => $value)
        {
            if (strpos($string, $key) !== FALSE)
            {
                $string = preg_replace("/$key" . '\b/', $attacherImage->getAttribute($value), $string);
            }
        }

        return $string;
    }

    /**
     * @param string $style
     * @param string $string
     * @return string
     */
    public function parseStyle($style, $string)
    {
        foreach ($this->getProcessingStylesRoutines() as $processing_styles_routine => $styles_array)
        {
            if (array_has($styles_array, $style))
            {
                $string = preg_replace('/:style\b/', $style, $string);
            }
        }

        return $string;
    }

    /**
     * @param AttacherImageContract $attacherImage
     * @param string|null           $style
     * @return string
     */
    public function parseDeletePath(AttacherImageContract $attacherImage, $style = NULL)
    {
        is_null($style)
            ? preg_match('/.*:owner_class\/:owner_id\b/', $this->getPath(), $delete_path)
            : preg_match('/.*:owner_class\/:owner_id\/:style\b/', $this->getPath(), $delete_path);


        $delete_path = $this->parseStyle($style, $delete_path[0]);
        $delete_path = $this->parseOwnerClass($attacherImage, $delete_path);
        $delete_path = $this->parseAttributes($attacherImage, $delete_path);

        return $delete_path;
    }

    /**
     * @param AttacherImageContract $attacherImage
     * @param null                  $style
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function parseUrl(AttacherImageContract $attacherImage, $style = NULL)
    {
        $url = $this->getBaseUrl() . $this->parsePath($attacherImage, $style);
        $url = preg_replace('~(^|[^:])//+~', '\\1/', $url);

        return url($url);
    }

    /**
     * @param AttacherImageContract $attacherImage
     * @return string|null
     */
    public function parsePreviousPath(AttacherImageContract $attacherImage)
    {
        return $attacherImage->hasDifferentFileName()
            ? $this->parseDeletePath($attacherImage, NULL)
            : NULL;
    }

    /** @return array */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param array $string
     * @return $this
     */
    public function setArguments($string)
    {
        $this->arguments = $string;

        return $this;
    }

    /** @return string */
    public function getBaseUrl()
    {
        return $this->base_url;
    }

    /** @return array */
    public function getProcessingStylesRoutines()
    {
        return $this->processing_styles_routines;
    }

    /** @return string */
    public function getPath()
    {
        return $this->path;
    }

}