<?php

use App\Models\ChatParticipant;
use Illuminate\Support\Facades\Broadcast;

// Customer order updates
Broadcast::channel("customers.{customerId}", fn ($user, $customerId) =>
    (int) $user->customer?->id === (int) $customerId);

// Branch dashboards (staff of that branch, or super admins)
Broadcast::channel("branches.{branchId}", fn ($user, $branchId) =>
    $user->hasRole("super-admin") || (int) $user->branch_id === (int) $branchId);

// Rider live location (the rider, branch staff and the order customer via app logic)
Broadcast::channel("riders.{riderId}.location", fn ($user, $riderId) =>
    $user->rider?->id === (int) $riderId || $user->hasAnyRole(["super-admin", "admin", "branch-manager"]));

// Internal team chat
Broadcast::channel("chat.{conversationId}", fn ($user, $conversationId) =>
    ChatParticipant::where("chat_conversation_id", $conversationId)->where("user_id", $user->id)->exists());
