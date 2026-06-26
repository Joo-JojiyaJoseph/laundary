<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('mobile', 20)->index();
            $table->unsignedTinyInteger('rating'); // 1..5
            $table->text('message');
            $table->boolean('is_verified')->default(false); // phone-verified via OTP
            $table->boolean('is_approved')->default(true);   // admin moderation flag
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
