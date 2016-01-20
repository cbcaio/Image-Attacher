<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttacherImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('attacher_images', function (Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('owner_id')->index();
            $table->string('owner_type')->index();
            $table->string('file_extension');
            $table->string("file_name")->nullable();
            $table->smallInteger("file_size", FALSE, TRUE)->nullable();
            $table->string("mime_type")->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('attacher_images');
    }
}
