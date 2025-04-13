<?php
use Illuminate\Database\Migrations\Migration;  
use Illuminate\Database\Schema\Blueprint;  
use Illuminate\Support\Facades\Schema;  

class CreateVicServiceTable extends Migration  
{  
    public function up()  
    {  
        Schema::create('vic_service', function (Blueprint $table) {  
            $table->id();  
            
            $table->unsignedBigInteger('service_id')->nullable();
            $table->foreign('service_id')
                  ->references('id')
                  ->on('services')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('vic_id')->nullable();
            $table->foreign('vic_id')
                  ->references('id')
                  ->on('vics')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('item_id')->nullable();
            $table->foreign('item_id')
                  ->references('id')
                  ->on('items')
                  ->onDelete('cascade');

            $table->integer('item_quantity')->nullable();
            
            $table->unsignedBigInteger('parking_slot_id');
            $table->foreign('parking_slot_id')
                  ->references('id')
                  ->on('parking_slots')
                  ->onDelete('cascade');

            $table->timestamps();  
        });  
    }  

    public function down()  
    {  
        Schema::dropIfExists('vic_service');  
    }  
}  