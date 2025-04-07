<?php
use Illuminate\Database\Migrations\Migration;  
use Illuminate\Database\Schema\Blueprint;  
use Illuminate\Support\Facades\Schema;  

class CreateVicsTable extends Migration  
{  
    public function up()  
    {  
        Schema::create('vics', function (Blueprint $table) {  
            $table->id();  
            $table->string('brand');  
            $table->string('typ'); // Assuming 'typ' stands for type, like 'car' or 'motorcycle'  
            $table->string('plate'); // Assuming 'palte' is the vehicle plate  
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');  
            $table->timestamps();  
        });  
    }  

    public function down()  
    {  
        Schema::dropIfExists('vics');  
    }  
}  