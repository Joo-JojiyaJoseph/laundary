<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('riders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('vehicle_number')->nullable();
            $table->decimal('current_lat', 10, 7)->nullable();
            $table->decimal('current_lng', 10, 7)->nullable();
            $table->timestamp('location_updated_at')->nullable();
            $table->boolean('is_online')->default(false);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->unique(); // ORD-00001
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('rider_id')->nullable()->constrained('riders')->nullOnDelete();
            $table->string('status')->default('pickup_scheduled')->index();
            $table->string('payment_status')->default('unpaid'); // unpaid|partial|paid
            $table->timestamp('pickup_at')->nullable();
            $table->timestamp('delivery_expected_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->string('coupon_code')->nullable();
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('delivery_otp', 6)->nullable();
            $table->string('signature_path')->nullable();
            $table->string('proof_photo_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('service_id')->constrained();
            $table->string('name');
            $table->decimal('qty', 8, 2)->default(1);
            $table->string('uom')->default('pc');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('line_total', 12, 2);
            $table->string('tag_code')->nullable()->index(); // garment barcode/QR
            $table->timestamps();
        });

        Schema::create('order_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique(); // INV-0001
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained();
            $table->string('tracking_token', 64)->unique(); // signed QR tracking
            $table->decimal('amount', 12, 2);
            $table->string('pdf_path')->nullable();
            $table->timestamp('issued_at');
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('method'); // cash|upi|card|bank|razorpay|stripe
            $table->string('type')->default('payment'); // payment|advance|refund
            $table->decimal('amount', 12, 2);
            $table->string('reference')->nullable(); // gateway txn id
            $table->string('status')->default('completed');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('order_status_logs');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('riders');
    }
};
