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
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('subscribe_id')->nullable()->after('remember_token');
            $table->timestamp('subscribe_expire_at')->nullable()->after('subscribe_id');

            $table->foreign('subscribe_id')->references('id')->on('subscribes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('subscribe_id');
            $table->dropColumn('subscribe_expire_at');
        });
    }
};
