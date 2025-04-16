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
        Schema::table('mouvement_stocks', function (Blueprint $table) {
            if (!Schema::hasColumn('mouvement_stocks', 'reference_document')) {
                $table->string('reference_document')->nullable()->after('motif');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mouvement_stocks', function (Blueprint $table) {
            if (Schema::hasColumn('mouvement_stocks', 'reference_document')) {
                $table->dropColumn('reference_document');
            }
        });
    }
};
