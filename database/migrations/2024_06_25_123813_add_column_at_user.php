<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //phoe
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('roles')->default('user');
            $table->string('license_plate')->nullable();
            $table->string('restaurant_name')->nullable();
            $table->string('restaurant_address')->nullable();
            $table->string('photo')->nullable();
            $table->string('latlong')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('phone');
            $table->dropColumn('address');
            $table->dropColumn('roles');
            $table->dropColumn('license_plate');
            $table->dropColumn('restaurant_name');
            $table->dropColumn('restaurant_address');
            $table->dropColumn('photo');
            $table->dropColumn('latlong');
        });
    }
};
