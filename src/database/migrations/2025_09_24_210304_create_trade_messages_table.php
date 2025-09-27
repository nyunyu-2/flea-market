<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('trade_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trade_id')->constrained()->onDelete('cascade'); // 取引ごとに紐づく
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // 誰が送ったか
            $table->text('message');                                          // 本文
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('trade_messages');
    }
};
