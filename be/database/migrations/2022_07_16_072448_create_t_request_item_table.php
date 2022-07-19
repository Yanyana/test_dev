<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTRequestItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_request_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('m_item');
            $table->foreignId('user_id')->constrained('users');
            $table->string('name', 150);
            $table->string('location', 150);
            $table->string('unit', 50);
            $table->float('qty_req');
            $table->string('note')->nullable();
            $table->timestamp('request_date')->index();
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
        Schema::dropIfExists('t_request_item');
    }
}
