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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('address');
            $table->dropColumn('street');
            $table->dropColumn('city');
            $table->dropColumn('province');
            $table->dropColumn('postal_code');
            $table->dropColumn('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('address');
            $table->string('street');
            $table->string('city');
            $table->string('province');
            $table->integer('postal_code');
            $table->string('phone');
        });
    }
};
