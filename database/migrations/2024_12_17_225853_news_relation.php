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
        Schema::enableForeignKeyConstraints();

        Schema::table('news', function (Blueprint $table) {
            $table->foreign('api_id')->references('id')->on('apis');
            $table->foreign('source_id')->references('id')->on('sources');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('news', function(Blueprint $table){
            $table->dropForeign(['api_id']);
            $table->dropForeign(['source_id']);
        });
    }
};
