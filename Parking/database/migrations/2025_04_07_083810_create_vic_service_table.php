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
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');  
            $table->foreignId('vic_id')->constrained('vics')->onDelete('cascade');  
            $table->foreignId('parking_slot_id')->constrained('parking_slots')->onDelete('cascade');  
            $table->timestamps();  
        });  
    }  

    public function down()  
    {  
        Schema::dropIfExists('vic_service');  
    }  
}  