<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // CUS-0001
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('mobile', 20)->index();
            $table->string('alternate_mobile', 20)->nullable();
            $table->string('email')->nullable();
            $table->date('birthday')->nullable();
            $table->string('city')->nullable();
            $table->string('pincode', 10)->nullable();
            $table->unsignedInteger('loyalty_points')->default(0);
            $table->string('loyalty_tier')->default('silver'); // silver|gold|platinum
            $table->string('referral_code')->nullable()->unique();
            $table->foreignId('referred_by')->nullable()->constrained('customers')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->boolean('is_vip')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('label')->default('Home');
            $table->text('address');
            $table->string('city')->nullable();
            $table->string('pincode', 10)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->integer('points'); // +earn / -redeem
            $table->string('type'); // order|referral|birthday|redeem|bonus
            $table->string('description')->nullable();
            $table->nullableMorphs('reference');
            $table->timestamps();
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type'); // percent|fixed|cashback
            $table->decimal('value', 10, 2);
            $table->decimal('min_order_amount', 10, 2)->default(0);
            $table->decimal('max_discount', 10, 2)->nullable();
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('loyalty_transactions');
        Schema::dropIfExists('customer_addresses');
        Schema::dropIfExists('customers');
    }
};
