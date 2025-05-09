<?php
use Illuminate\Database\Migrations\Migration;  
use Illuminate\Database\Schema\Blueprint;  
use Illuminate\Support\Facades\Schema;  

class CreatePricesTable extends Migration  
{  
    public function up()  
    {  
        Schema::create('prices', function (Blueprint $table) {  
            $table->id();  
            $table->decimal('car_price', 8, 2)->default(0);  
            $table->decimal('moto_price', 8, 2)->default(0);  
            $table->timestamps();  
        });  
    }  

    public function down()  
    {  
        Schema::dropIfExists('prices');  
    }  
}  