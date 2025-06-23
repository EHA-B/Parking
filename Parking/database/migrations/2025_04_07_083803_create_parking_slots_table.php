<?php
use Illuminate\Database\Migrations\Migration;  
use Illuminate\Database\Schema\Blueprint;  
use Illuminate\Support\Facades\Schema;  

class CreateParkingSlotsTable extends Migration  
{  
    public function up()  
    {  
        Schema::create('parking_slots', function (Blueprint $table) {  
            $table->id();  
            $table->integer('price')->nullable();
            $table->foreignId('vic_id')->constrained('vics')->onDelete('cascade');  
            $table->string('parcode'); // Assuming this is for barcode  
            $table->timestamp('time_in')->nullable();  
            $table->timestamp('time_out')->nullable()->default(null); // Default null for time out  
            $table->string('notes')->nullable();
            $table->enum('status', ['in', 'out'])->default('in')->after('parking_type');
            $table->enum('parking_type', ['hourly', 'daily', 'monthly'])->default('hourly');
            $table->timestamps();  
        });  
    }  

    public function down()  
    {  
        Schema::dropIfExists('parking_slots');  
    }  
}  