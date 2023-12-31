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
        Schema::create('threads', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('text');
            $table->string('name')->default('');
            $table->boolean('enabled')->default(true);
            $table->string('slug')->default('')->unique();
            $table->foreignIdFor(\App\Models\Group::class, 'group_id')->default(null);
            $table->foreignIdFor(\App\Models\User::class, 'created_by')->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('threads', function (Blueprint $table) {
            //
        });
    }
};
