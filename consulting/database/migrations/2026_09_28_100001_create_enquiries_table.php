<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('type', 32)->index();          // contact | consultation
            $table->string('name');
            $table->string('email');
            $table->string('phone', 40)->nullable();
            $table->string('company')->nullable();
            $table->string('service', 120)->nullable();
            $table->string('budget', 60)->nullable();
            $table->text('message');

            // Attachment lives on the private disk; only the key is stored.
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->unsignedInteger('attachment_size')->nullable();
            $table->string('attachment_mime', 100)->nullable();

            // Kept for abuse triage only, and pruned on a schedule.
            $table->string('ip_hash', 64)->nullable()->index();
            $table->string('user_agent', 255)->nullable();

            $table->timestamp('handled_at')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};
