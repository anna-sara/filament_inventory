<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Item;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('reserveditems', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->integer('user_id')->nullable();
            $table->string('username')->nullable();
            $table->timestamp('reserved_date')->nullable();
            $table->timestamp('delivered_date')->nullable();
            $table->timestamp('return_date')->nullable();
            $table->timestamp('returned_date')->nullable();
            $table->boolean('delivered')->default(false);
            $table->boolean('returned')->default(false);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserveditems');
    }
};
