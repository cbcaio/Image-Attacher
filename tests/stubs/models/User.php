<?php
namespace CbCaio\ImgAttacher\Testing;

use CbCaio\ImgAttacher\Traits\HasImage;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use hasImage;
}