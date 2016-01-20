<?php
# config/img-attacher.php

/*
 * 'base_url': works as a prefix for the path. Ex: in case your are using 'local' driver in your 'config/flysystem.php'
 * and has path defined to save in public_path('files'), you will need to define 'base_url' as 'file'.
 *
 * 'path_to_save': You can change this, but do not remove ':owner_class/:owner_id'! Will break delete method.
 *
 * 'processing_styles_routine': You will need to specify one of these 'routines' to the addImage method of the
 * hasImage trait, each style represents a file which will be saved as a copy of the original image after being
 * processed by the closure.
 */

return [
    'model'        => 'CbCaio\ImgAttacher\Models\AttacherImage',
    'base_url'     => 'files',
    'path_to_save' => '/uploads/images/:owner_class/:owner_id/:style/:filename',

    'processing_styles_routines' => [
        'default_routine' =>
        [
            'original_style' => function ($image) {
                return $image;
            },
            'original_style2' => function ($image) {
                return $image;
            },
        ],
        'default_routine2' =>
        [
            'original_style3' => function ($image) {
                return $image;
            },
            'original_style4' => function ($image) {
                return $image;
            },
        ],
    ]
];
