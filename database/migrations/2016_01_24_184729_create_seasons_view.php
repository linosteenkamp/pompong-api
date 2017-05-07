<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeasonsView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('create view seasons as (select cast(concat(9, season, show_id) as SIGNED) as id, show_id AS show_id, season AS season, sum(file_size) AS file_size from episodes where status = \'downloaded\' group by show_id, season)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('drop view seasons');
    }
}