<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('desc')->nullable();
            $table->date('acquisition_date')->nullable();
            $table->string('image')->nullable();
            $table->integer('type_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('cost')->nullable();
            $table->boolean('can_be_loaned')->default(false);
            $table->boolean('reserved')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
