<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_no')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject');
            $table->string('category')->nullable(); // ai-categorized
            $table->string('priority')->default('normal');
            $table->string('status')->default('open'); // open|in_progress|resolved|closed
            $table->unsignedTinyInteger('rating')->nullable();
            $table->timestamps();
        });

        Schema::create('support_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_ticket_id')->constrained()->cascadeOnDelete();
            $table->morphs('sender'); // customer or user
            $table->text('body');
            $table->json('attachments')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('type')->default('direct'); // direct|group|branch
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('chat_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();
            $table->unique(['chat_conversation_id', 'user_id']);
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->json('attachments')->nullable();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('key')->index();
            $table->text('value')->nullable();
            $table->timestamps();
            $table->unique(['branch_id', 'key']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_participants');
        Schema::dropIfExists('chat_conversations');
        Schema::dropIfExists('support_messages');
        Schema::dropIfExists('support_tickets');
    }
};
