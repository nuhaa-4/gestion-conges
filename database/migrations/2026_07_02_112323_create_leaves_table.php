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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            
            // On lie le congé à l'utilisateur qui le demande (Clé étrangère)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Les dates de début et de fin de congé
            $table->date('start_date');
            $table->date('end_date');
            
            // Le type de congé (Ex: Payé, Maladie) et le motif optionnel
            $table->string('type');
            $table->text('reason')->nullable();
            
            // Le statut de la demande : par défaut elle est en attente ('pending')
            // Elle pourra passer à 'approved' (validée) ou 'rejected' (refusée)
            $table->string('status')->default('pending');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};