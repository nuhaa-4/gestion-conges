<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leave;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        // Si l'utilisateur connecté est un manager, on le redirige vers son espace
        if ($request->user()->role === 'manager') {
            return redirect()->route('manager.dashboard');
        }

        // Sinon, on récupère ses demandes de congés classées de la plus récente à la plus ancienne
        $leaves = Leave::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard', compact('leaves'));
    }

    // Cette fonction sert à enregistrer la demande de congé dans la base de données
    public function store(Request $request)
    {
        // 1. On vérifie que les données du formulaire sont correctes
        $rules = [
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|string',
            'reason' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:2048', // 2 Mo max
        ];

        if ($request->type === 'Congé de Maladie') {
            $rules['start_date'] = 'required|date';
        } else {
            $rules['start_date'] = 'required|date|after_or_equal:today';
        }

        $request->validate($rules);

        // Validation contre le chevauchement de congés (double réservation)
        $overlappingLeave = Leave::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                      });
            })
            ->exists();

        if ($overlappingLeave) {
            return redirect()->back()->withErrors([
                'start_date' => 'Vous avez déjà une demande de congé en attente ou validée qui chevauche cette période.'
            ])->withInput();
        }

        // Validation des durées imposées par la législation marocaine
        $fixedDurations = [
            'Congé de Maternité' => 98,
            'Congé de Paternité' => 3,
            'Mariage du salarié' => 4,
            'Mariage d\'un enfant' => 2,
            'Décès du conjoint, d\'un enfant ou d\'un parent' => 3,
            'Décès d\'un frère, d\'une sœur ou d\'un grand-parent' => 2,
            'Circoncision d\'un enfant' => 2
        ];

        if (isset($fixedDurations[$request->type])) {
            $expectedDays = $fixedDurations[$request->type];
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $actualDays = $startDate->diffInDays($endDate) + 1;

            if ($actualDays != $expectedDays) {
                return redirect()->back()->withErrors([
                    'end_date' => "La durée de ce congé est imposée : {$expectedDays} jours (saisi : {$actualDays} jours)."
                ])->withInput();
            }
        }

        // 2. Gestion du justificatif
        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('documents', 'public');
        }

        // 3. On crée la demande en base de données
        Leave::create([
            'user_id' => Auth::id(), // On récupère l'ID de l'employé connecté
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'type' => $request->type,
            'reason' => $request->reason,
            'document_path' => $documentPath,
            // Le statut se mettra à 'pending' tout seul grâce à notre migration !
        ]);

        // 4. On renvoie l'employé sur son tableau de bord avec un message de succès
        return redirect()->route('dashboard')->with('success', 'Votre demande de congé a bien été soumise !');
    }

    // Affiche le tableau de bord du Manager avec les demandes en attente
    public function managerDashboard(Request $request)
    {
        if ($request->user()->role !== 'manager') {
            return redirect()->route('dashboard')->with('error', 'Accès refusé. Vous devez être un manager.');
        }

        // Récupère le mois sélectionné (ou mois en cours par défaut)
        $selectedMonth = $request->input('month', now()->format('Y-m'));
        $startDate = Carbon::parse($selectedMonth)->startOfMonth();
        $endDate = Carbon::parse($selectedMonth)->endOfMonth();

        // Compte le nombre de salariés s'étant absentés (demande approuvée) pendant le mois sélectionné
        $absentEmployeesCount = Leave::where('status', 'approved')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })
            ->distinct('user_id')
            ->count('user_id');

        // Récupère toutes les demandes "pending" avec la relation user
        $leaves = Leave::where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        // Récupère l'historique de toutes les demandes traitées (approuvées ou refusées)
        $processedLeaves = Leave::whereIn('status', ['approved', 'rejected'])
            ->with('user')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('manager.dashboard', compact('leaves', 'absentEmployeesCount', 'selectedMonth', 'processedLeaves'));
    }

    // Approuver une demande de congé
    public function approve(Request $request, Leave $leave)
    {
        if ($request->user()->role !== 'manager') {
            abort(403, 'Action non autorisée.');
        }

        // Vérification de la concurrence d'état
        if ($leave->status !== 'pending') {
            return redirect()->back()->with('error', 'Cette demande de congé a déjà été traitée par un autre gestionnaire.');
        }

        // Vérification d'auto-approbation
        if ($leave->user_id === $request->user()->id) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas valider ou refuser votre propre demande de congé.');
        }

        $request->validate([
            'manager_comment' => 'nullable|string|max:1000',
        ]);

        $leave->update([
            'status' => 'approved',
            'manager_comment' => $request->manager_comment,
        ]);

        return redirect()->back()->with('success', 'La demande de congé a été approuvée.');
    }

    // Refuser une demande de congé
    public function reject(Request $request, Leave $leave)
    {
        if ($request->user()->role !== 'manager') {
            abort(403, 'Action non autorisée.');
        }

        // Vérification de la concurrence d'état
        if ($leave->status !== 'pending') {
            return redirect()->back()->with('error', 'Cette demande de congé a déjà été traitée par un autre gestionnaire.');
        }

        // Vérification d'auto-approbation
        if ($leave->user_id === $request->user()->id) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas valider ou refuser votre propre demande de congé.');
        }

        $request->validate([
            'manager_comment' => 'nullable|string|max:1000',
        ]);

        $leave->update([
            'status' => 'rejected',
            'manager_comment' => $request->manager_comment,
        ]);

        return redirect()->back()->with('success', 'La demande de congé a été refusée.');
    }
}