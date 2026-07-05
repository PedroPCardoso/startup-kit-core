<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('startup_kit_outbox', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('aggregate_type')->nullable();
            $table->string('aggregate_id')->nullable()->index();
            $table->string('event_type');
            $table->longText('payload');
            $table->string('status', 20)->default('pending')->index();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'attempts', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('startup_kit_outbox');
    }
};
