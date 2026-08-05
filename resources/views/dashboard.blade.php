<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de bord - Demande de Congés') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                <h3 class="text-lg font-medium text-gray-900 mb-4">Soumettre une nouvelle demande</h3>

                <form action="{{ route('leaves.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date de début</label>
                            <input type="date" name="start_date" id="start_date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date de fin</label>
                            <input type="date" name="end_date" id="end_date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type de congé</label>
                            <select name="type" id="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="Congé Annuel Payé">Le Congé Annuel Payé (Vacances)</option>
                                <option value="Congé de Maternité">Le Congé de Maternité (14 semaines / 98 jours)</option>
                                <option value="Congé de Paternité">Le Congé de Paternité (3 jours)</option>
                                <option value="Congé de Maladie">Le Congé de Maladie</option>
                                <option value="Mariage du salarié">Mariage du salarié (4 jours)</option>
                                <option value="Mariage d'un enfant">Mariage d'un enfant (2 jours)</option>
                                <option value="Décès du conjoint, d'un enfant ou d'un parent">Décès du conjoint, d'un enfant ou d'un parent (3 jours)</option>
                                <option value="Décès d'un frère, d'une sœur ou d'un grand-parent">Décès d'un frère, d'une sœur ou d'un grand-parent (2 jours)</option>
                                <option value="Circoncision d'un enfant">Circoncision d'un enfant (2 jours)</option>
                                <option value="Congé de Récupération">Le Congé de Récupération</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Motif / Commentaire (optionnel)</label>
                            <input type="text" name="reason" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Justificatif / Certificat médical (optionnel)</label>
                            <div class="flex items-center space-x-2 mt-1">
                                <input type="file" name="document" id="document" class="block w-full text-sm text-gray-500 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded-l-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <button type="button" id="clear-document" class="hidden inline-flex items-center px-3 py-2 bg-red-100 text-red-700 hover:bg-red-200 rounded-md text-xs font-semibold transition duration-150">
                                    Effacer
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-primary-button>
                            {{ __('Envoyer la demande') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Historique des demandes -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Historique de mes demandes</h3>

                @if ($leaves->isEmpty())
                    <p class="text-gray-500 text-sm">Vous n'avez soumis aucune demande de congé pour le moment.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Début</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Fin</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motif / Commentaire</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Justificatif</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Demande</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commentaire Manager</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($leaves as $leave)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            {{ $leave->type }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($leave->start_date)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($leave->end_date)->format('d/m/Y') }}
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if ($leave->status === 'pending')
                                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    En attente
                                                </span>
                                            @elseif ($leave->status === 'approved')
                                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Validée
                                                </span>
                                            @elseif ($leave->status === 'rejected')
                                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Refusée
                                                </span>
                                            @endif
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const typeSelect = document.getElementById('type');
            const documentInput = document.getElementById('document');
            const clearDocumentButton = document.getElementById('clear-document');

            // Logique de suppression du document sélectionné
            documentInput.addEventListener('change', function () {
                if (documentInput.files && documentInput.files.length > 0) {
                    clearDocumentButton.classList.remove('hidden');
                } else {
                    clearDocumentButton.classList.add('hidden');
                }
            });

            clearDocumentButton.addEventListener('click', function () {
                documentInput.value = '';
                clearDocumentButton.classList.add('hidden');
            });

            // Durées imposées pour chaque type de congé
            const fixedDurations = {
                'Congé de Maternité': 98,
                'Congé de Paternité': 3,
                'Mariage du salarié': 4,
                'Mariage d\'un enfant': 2,
                'Décès du conjoint, d\'un enfant ou d\'un parent': 3,
                'Décès d\'un frère, d\'une sœur ou d\'un grand-parent': 2,
                'Circoncision d\'un enfant': 2
            };

            function calculateEndDate() {
                const type = typeSelect.value;
                const startDateVal = startDateInput.value;

                if (fixedDurations[type]) {
                    const duration = fixedDurations[type];
                    if (startDateVal) {
                        const startDate = new Date(startDateVal);
                        const endDate = new Date(startDate);
                        // end_date = start_date + (duration - 1) jours
                        endDate.setDate(startDate.getDate() + duration - 1);
                        
                        // Format YYYY-MM-DD
                        const year = endDate.getFullYear();
                        const month = String(endDate.getMonth() + 1).padStart(2, '0');
                        const day = String(endDate.getDate()).padStart(2, '0');
                        
                        endDateInput.value = `${year}-${month}-${day}`;
                        endDateInput.readOnly = true;
                        endDateInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                    } else {
                        endDateInput.value = '';
                    }
                } else {
                    endDateInput.readOnly = false;
                    endDateInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
                }
            }

            typeSelect.addEventListener('change', calculateEndDate);
            startDateInput.addEventListener('change', calculateEndDate);
        });
    </script>
</x-app-layout>