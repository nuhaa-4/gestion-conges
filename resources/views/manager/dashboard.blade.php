<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Espace Validation des Congés (Manager)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Section Statistiques d'absentéisme -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Filtre de mois -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col justify-between">
                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Filtrer par mois</h4>
                    <form action="{{ route('manager.dashboard') }}" method="GET" class="flex gap-2">
                        <input type="month" name="month" value="{{ $selectedMonth }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 text-center">
                            Filtrer
                        </button>
                    </form>
                </div>

                <!-- Nombre d'employés absents -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 col-span-2 flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Nombre d'employés absents</h4>
                        <p class="text-xs text-gray-400">Pour la période de : {{ \Carbon\Carbon::parse($selectedMonth)->format('m/Y') }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-red-100 rounded-full text-red-600">
                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-3xl font-extrabold text-gray-900">{{ $absentEmployeesCount }}</span>
                            <span class="text-sm text-gray-500 block">salarié(s) absent(s)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des demandes en attente -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Demandes de congés en attente</h3>

                @if ($leaves->isEmpty())
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="mt-4 text-gray-500 text-sm font-medium">Aucune demande de congé en attente de validation.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employé</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motif</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Justificatif</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Demande</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commentaire Validation</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($leaves as $leave)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-gray-900">{{ $leave->user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $leave->user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $leave->type }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            Du {{ \Carbon\Carbon::parse($leave->start_date)->format('d/m/Y') }}<br>
                                            au {{ \Carbon\Carbon::parse($leave->end_date)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $leave->reason }}">
                                            {{ $leave->reason ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if ($leave->document_path)
                                                <a href="{{ asset('storage/' . $leave->document_path) }}" target="_blank" class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-900">
                                                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    Voir
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                            {{ $leave->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <input type="text" id="row-comment-{{ $leave->id }}" placeholder="Ajouter un motif/commentaire..." class="text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full min-w-[150px]">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="inline-flex space-x-2">
                                                <!-- Formulaire de validation -->
                                                <form action="{{ route('leaves.approve', $leave) }}" method="POST" onsubmit="document.getElementById('approve-comment-{{ $leave->id }}').value = document.getElementById('row-comment-{{ $leave->id }}').value; return confirm('Voulez-vous vraiment approuver cette demande de congé ?');">
                                                    @csrf
                                                    <input type="hidden" name="manager_comment" id="approve-comment-{{ $leave->id }}">
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                        Valider
                                                    </button>
                                                </form>

                                                <!-- Formulaire de refus -->
                                                <form action="{{ route('leaves.reject', $leave) }}" method="POST" onsubmit="document.getElementById('reject-comment-{{ $leave->id }}').value = document.getElementById('row-comment-{{ $leave->id }}').value; return confirm('Voulez-vous vraiment refuser cette demande de congé ?');">
                                                    @csrf
                                                    <input type="hidden" name="manager_comment" id="reject-comment-{{ $leave->id }}">
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                        Refuser
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Historique des demandes traitées -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Historique des demandes traitées</h3>

                @if ($processedLeaves->isEmpty())
                    <p class="text-gray-500 text-sm">Aucune demande n'a été traitée pour le moment.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employé</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motif</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Justificatif</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Décision</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commentaire Manager</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($processedLeaves as $leave)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-gray-900">{{ $leave->user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $leave->user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $leave->type }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            Du {{ \Carbon\Carbon::parse($leave->start_date)->format('d/m/Y') }}<br>
                                            au {{ \Carbon\Carbon::parse($leave->end_date)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $leave->reason }}">
                                            {{ $leave->reason ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if ($leave->document_path)
                                                <a href="{{ asset('storage/' . $leave->document_path) }}" target="_blank" class="inline-flex items-center text-xs font-semibold text-indigo-600 hover:text-indigo-900">
                                                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    Voir
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if ($leave->status === 'approved')
                                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Validée
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Refusée
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                            {{ $leave->updated_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $leave->manager_comment }}">
                                            {{ $leave->manager_comment ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
