<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKriteriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kriterias', function (Blueprint $table) {
            
            $table->id();
            $table->string("name");
            $table->string("interval");
            $table->integer("bobot");
            $table->enum("formula",["maut","saw","irap"]);
            $table->string("type")->nullable();
            $table->timestamps();

            $table->foreignId("tematik_id")
            ->on('tematiks')
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
        Schema::dropIfExists('kriterias');
    }
}
