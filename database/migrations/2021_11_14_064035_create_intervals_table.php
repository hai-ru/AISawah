<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntervalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intervals', function (Blueprint $table) {
            $table->id();
            $table->integer("minimum")->nullbale();
            $table->integer("maximum")->nullbale();
            $table->integer("bobot");
            $table->string("condition")->nullbale();
            $table->timestamps();

            $table->foreignId("kriteria_id")
            ->on('kriterias')
            ->constrained()
            ->onUpdate('cascade')
            ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('intervals');
    }
}
