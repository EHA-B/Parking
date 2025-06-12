<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('parking_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parking_slot_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['in', 'out']);
            $table->timestamp('changed_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('parking_status_histories');
    }
};