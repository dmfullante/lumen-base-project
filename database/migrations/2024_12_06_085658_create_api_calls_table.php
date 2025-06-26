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
        Schema::create('api_calls', function (Blueprint $table) {
            $table->id();
            $table->string('service_from')->nullable();
            $table->string('service_to')->nullable();
            $table->longText('request')->nullable();
            $table->longText('response')->nullable();
            $table->string('status', 32)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['service_from']);
            $table->index(['service_to']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_calls');
    }
};
