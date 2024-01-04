<?php

use App\Models\Reference\Region;
use App\Models\Reference\SubRegion;
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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Region::class)
                ->nullable()
                ->constrained();
            $table->foreignIdFor(SubRegion::class)
                ->nullable()
                ->constrained();
            $table->string('name');
            $table->string('iso3');
            $table->string('iso2');
            $table->string('numeric_code');
            $table->string('phone_code');
            $table->string('capital');
            $table->string('tld');
            $table->string('native')
                ->nullable();
            $table->string('nationality');
            $table->json('translations')
                ->nullable();
            $table->string('latitude');
            $table->string('longitude');
            $table->string('emoji');
            $table->string('emojiU');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
