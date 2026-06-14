<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (! Schema::hasColumn('services', 'product_category_id')) {
                $table->foreignId('product_category_id')->nullable()->after('id')
                    ->constrained()->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'product_category_id')) {
                $table->dropConstrainedForeignId('product_category_id');
            }
        });
    }
};
