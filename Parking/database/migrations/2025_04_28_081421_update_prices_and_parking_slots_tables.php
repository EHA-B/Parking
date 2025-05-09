<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->dropColumn(['car_price', 'moto_price']);
            $table->decimal('car_hourly_rate', 10, 2)->default(0);
            $table->decimal('car_daily_rate', 10, 2)->default(0);
            $table->decimal('car_monthly_rate', 10, 2)->default(0);
            $table->decimal('moto_hourly_rate', 10, 2)->default(0);
            $table->decimal('moto_daily_rate', 10, 2)->default(0);
            $table->decimal('moto_monthly_rate', 10, 2)->default(0);
        });

        Schema::table('parking_slots', function (Blueprint $table) {
            $table->enum('parking_type', ['hourly', 'daily', 'monthly'])->default('hourly');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->dropColumn([
                'car_hourly_rate',
                'car_daily_rate',
                'car_monthly_rate',
                'moto_hourly_rate',
                'moto_daily_rate',
                'moto_monthly_rate'
            ]);
            $table->decimal('car_price', 10, 2)->default(0);
            $table->decimal('moto_price', 10, 2)->default(0);
        });

        Schema::table('parking_slots', function (Blueprint $table) {
            $table->dropColumn('parking_type');
        });
    }
};
