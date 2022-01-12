<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModelPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('model_permission', function (Blueprint $table) {
            $table->string('permission');
            $table->string('model_type');
            $table->string('model_id'); // for cases where the model id is uuid
            $table->timestamps();

            $table->unique(['permission', 'model_id', 'model_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('model_permission');
    }
}
