<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shows', function (Blueprint $table) {
            $table->integer('id');
            $table->string('lang'); 
            $table->string('network'); 
            $table->string('quality'); 
            $table->string('show_name'); 
            $table->string('status'); 
            $table->integer('tvdb_id'); 
            $table->string('image_url'); 
            $table->text('overview');
            $table->string('location');
            $table->integer('max_season');
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
        Schema::drop('shows');
    }
}
