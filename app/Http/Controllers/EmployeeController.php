<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class EmployeeController extends Controller
{
    /**
     * Affiche la liste des salariés (Réservé au Manager).
     */
    public function index(Request $request)
    {
        if ($request->user()->role !== 'manager') {
            return redirect()->route('dashboard')->with('error', 'Accès refusé. Vous devez être un manager.');
        }

        // On récupère tous les salariés avec le nombre total de demandes de congés qu'ils ont faites
        $employees = User::withCount('leaves')->orderBy('name', 'asc')->get();

        return view('manager.employees.index', compact('employees'));
    }

    /**
     * Affiche le formulaire de modification d'un salarié.
     */
    public function edit(Request $request, User $user)
    {
        if ($request->user()->role !== 'manager') {
            return redirect()->route('dashboard')->with('error', 'Accès refusé. Vous devez être un manager.');
        }

        $leaves = $user->leaves()->orderBy('created_at', 'desc')->get();

        return view('manager.employees.edit', compact('user', 'leaves'));
    }

    /**
     * Met à jour les informations du salarié en base de données.
     */
    public function update(Request $request, User $user)
    {
        if ($request->user()->role !== 'manager') {
            return redirect()->route('dashboard')->with('error', 'Accès refusé. Vous devez être un manager.');
        }

        // Validation des données
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:employee,manager',
        ]);

        // Mise à jour de l'employé
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        return redirect()->route('manager.employees.index')->with('success', 'La fiche du salarié ' . $user->name . ' a été mise à jour.');
    }
}
