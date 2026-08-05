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
            // On ajoute une colonne de texte pour le rôle. 
            // Par défaut, si on ne précise rien, l'utilisateur sera un 'employee'.
            $table->string('role')->default('employee')->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Si on annule, on supprime la colonne role
            $table->dropColumn('role');
        });
    }
};