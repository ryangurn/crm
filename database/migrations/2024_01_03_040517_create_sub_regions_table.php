<?php

use App\Models\Reference\Region;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sub_regions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Region::class)
                ->constrained();
            $table->string('name');
            $table->json('translations')
        ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_regions');
    }
};
