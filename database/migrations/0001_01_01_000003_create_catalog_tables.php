<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->json('benefits')->nullable();
            $table->json('faqs')->nullable();
            $table->decimal('starting_price', 10, 2)->nullable();
            $table->string('turnaround')->nullable(); // e.g. "24h"
            $table->unsignedInteger('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('priority')->default(0);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g. Shirt, Saree, Blanket
            $table->string('uom')->default('pc'); // pc|kg|pair|set
            $table->decimal('price', 10, 2);
            $table->string('image')->nullable();
            $table->unsignedInteger('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('price_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->default('branch'); // branch|customer|vip|seasonal|promo
            $table->decimal('price', 10, 2);
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['product_id', 'branch_id', 'type']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('price_lists');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('services');
    }
};
