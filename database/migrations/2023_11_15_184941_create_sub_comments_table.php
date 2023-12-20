<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sub_comments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('author')->default('');
            $table->text('text');
            $table->foreignIdFor(\App\Models\Thread::class, 'thread_id')->default(null);
            $table->foreignIdFor(\App\Models\User::class, 'created_by')->default(null);
            $table->foreignIdFor(\App\Models\Comment::class, 'comment_id')->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_comments');
    }
};
