<?php

use App\Models\Reference\Timezone;
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
        Schema::create('timezone_mappings', function (Blueprint $table) {
            $table->foreignIdFor(Timezone::class)
                ->constrained();
            $table->morphs('related');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timezone_mappings');
    }
};
