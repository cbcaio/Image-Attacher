<?php

namespace CbCaio\ImgAttacher\Models;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class AttacherImage extends AbstractAttacherImage
{
    protected $table = 'attacher_images';

    /**
     * @var string
     */
    protected $processing_style_routine = 'default_routine';

}
