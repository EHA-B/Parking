<?php
use Illuminate\Database\Migrations\Migration;  
use Illuminate\Database\Schema\Blueprint;  
use Illuminate\Support\Facades\Schema;  

class CreateSlotsTable extends Migration  
{  
    public function up()  
    {  
        Schema::create('slots', function (Blueprint $table) {  
            $table->id();  
            $table->string('status');  
            $table->timestamps();  
        });  
    }  

    public function down()  
    {  
        Schema::dropIfExists('slots');  
    }  
}  