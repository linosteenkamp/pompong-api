<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeasonUserPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('season_user', function (Blueprint $table) {
            $table->integer('season_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->primary(['season_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('season_user');
    }
}
