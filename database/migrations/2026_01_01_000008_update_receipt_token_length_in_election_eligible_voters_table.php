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
        Schema::table('election_eligible_voters', function (Blueprint $table) {
            $table->string('receipt_token', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('election_eligible_voters', function (Blueprint $table) {
            $table->string('receipt_token', 64)->nullable()->change();
        });
    }
};
