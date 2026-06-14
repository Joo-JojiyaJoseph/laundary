<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('mobile', 20)->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('google_id')->nullable()->index();
            $table->string('avatar')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            $table->text('two_factor_secret')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->string('mobile', 20)->index();
            $table->string('code', 6);
            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('login_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('device')->nullable();
            $table->string('platform')->nullable();
            $table->string('location')->nullable();
            $table->boolean('successful')->default(true);
            $table->timestamp('logged_in_at');
            $table->timestamps();
        });

        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('token', 512);
            $table->string('platform')->default('web');
            $table->timestamps();
            $table->unique(['user_id', 'token']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('device_tokens');
        Schema::dropIfExists('login_histories');
        Schema::dropIfExists('otp_codes');
        Schema::dropIfExists('users');
    }
};
