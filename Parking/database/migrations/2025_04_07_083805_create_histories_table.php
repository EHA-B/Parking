<?php
use Illuminate\Database\Migrations\Migration;  
use Illuminate\Database\Schema\Blueprint;  
use Illuminate\Support\Facades\Schema;  


class CreateHistoriesTable extends Migration  
{  
    public function up()  
    {  
        Schema::create('histories', function (Blueprint $table) {  
            $table->id();  
            $table->string('customer_name');  
            $table->string('vic_typ');  
            $table->string('vic_plate');  
            $table->timestamp('time_in')->nullable();  
            $table->timestamp('time_out')->nullable();  
            $table->decimal('price', 8, 2); 
            $table->json('services')->nullable(); // New JSON column for services
            $table->integer('duration')->nullable();
            $table->string('notes');
            $table->string('parking_type');
            $table->timestamps();  
        });  
    }  

    public function down()  
    {  
        Schema::dropIfExists('histories');  
    }  
}  