<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->integer("fact_index")->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->float('long')->nullable();
            $table->float('lat')->nullable();
            $table->string('era')->nullable();
            $table->string('source_links')->nullable();
            $table->string('location')->nullable();
            $table->text('history_fact')->nullable();
            $table->dateTime("start_date")->nullable();
            $table->dateTime("end_date")->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('author_id')->references('id')->on('authors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            //
        });
    }
}
