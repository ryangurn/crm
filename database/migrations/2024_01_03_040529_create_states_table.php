<?php

use App\Models\Reference\Country;
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
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Country::class)
                ->constrained();
            $table->string('name');
            $table->string('state_code');
            $table->string('type')
                ->nullable();
            $table->string('latitude')
                ->nullable();
            $table->string('longitude')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
