<?php
use Illuminate\Database\Migrations\Migration;  
use Illuminate\Database\Schema\Blueprint;  
use Illuminate\Support\Facades\Schema;  

class CreateServicesTable extends Migration  
{  
    public function up()  
    {  
        Schema::create('services', function (Blueprint $table) {  
            $table->id();  
            $table->string('name')->unique();  
            $table->decimal('cost', 10, 2);  
            $table->softDeletes();  
            $table->timestamps();  
        });  
    }  

    public function down()  
    {  
        Schema::dropIfExists('services');  
    }  
}  