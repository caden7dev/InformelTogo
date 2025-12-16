@extends('layouts.admin')

@section('title', 'Envoyer une Notification')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Envoyer une Notification</h1>
        <p class="text-gray-600 mt-2">Envoyez des notifications aux utilisateurs de la plateforme</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Formulaire d'envoi -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <form id="sendNotificationForm" class="space-y-6">
                    @csrf
                    
                    <!-- Destinataires -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Destinataires</label>
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <select id="recipientType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="single">Utilisateur spécifique</option>
                                        <option value="all">Tous les utilisateurs</option>
                                        <option value="role">Par rôle</option>
                                    </select>
                                </div>
                                
                                <div id="recipientSelection">
                                    <select id="user_id" name="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">Sélectionnez un utilisateur</option>
                                        @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Titre et Message -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Titre</label>
                            <input type="text" id="title" name="title" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Titre de la notification" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                            <textarea id="message" name="message" rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Contenu du message..." required></textarea>
                        </div>
                    </div>

                    <!-- Type et Options -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type de notification</label>
                            <select id="type" name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="info">Information</option>
                                <option value="success">Succès</option>
                                <option value="warning">Avertissement</option>
                                <option value="error">Erreur</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Icône</label>
                            <input type="text" id="icon" name="icon" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="fa-bell (optionnel)">
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div class="flex justify-end space-x-4 pt-6 border-t">
                        <button type="button" onclick="resetForm()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Annuler
                        </button>
                        <button type="button" onclick="sendNotification()" 
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            Envoyer la notification
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Prévisualisation -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Prévisualisation</h3>
                
                <div id="notificationPreview" class="space-y-4">
                    <!-- La prévisualisation sera mise à jour en JavaScript -->
                </div>
                
                <!-- Statistiques -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques</h3>
                    <div id="notificationStats" class="space-y-3">
                        <!-- Les statistiques seront chargées en JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Historique des notifications -->
    <div class="mt-8 bg-white rounded-2xl shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Notifications Récentes</h2>
            <button onclick="loadNotifications()" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium flex items-center space-x-1">
                <i class="fas fa-sync-alt"></i>
                <span>Rafraîchir</span>
            </button>
        </div>
        
        <div id="notificationsList">
            <!-- Les notifications seront chargées ici -->
        </div>
    </div>
</div>

<!-- Modal de confirmation -->
<div id="confirmationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-2xl bg-white">
        <div class="text-center">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-green-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2" id="modalTitle">Succès</h3>
            <p class="text-gray-600 mb-6" id="modalMessage"></p>
            <button onclick="closeConfirmationModal()" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition-colors">
                Fermer
            </button>
        </div>
    </div>
</div>

<!-- Modal de chargement -->
<div id="loadingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-2xl bg-white">
        <div class="text-center">
            <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-spinner fa-spin text-indigo-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Envoi en cours</h3>
            <p class="text-gray-600">Veuillez patienter...</p>
        </div>
    </div>
</div>

<script>
    // Variables globales
    let users = @json($users ?? []);
    
    // Initialisation
    document.addEventListener('DOMContentLoaded', function() {
        loadNotificationStats();
        loadNotifications();
        updatePreview();
        
        // Événements pour la prévisualisation
        document.getElementById('title').addEventListener('input', updatePreview);
        document.getElementById('message').addEventListener('input', updatePreview);
        document.getElementById('type').addEventListener('change', updatePreview);
        document.getElementById('recipientType').addEventListener('change', updateRecipientSelection);
        
        // Initialiser la sélection des destinataires
        updateRecipientSelection();
    });
    
    // Mettre à jour la sélection des destinataires
    function updateRecipientSelection() {
        const type = document.getElementById('recipientType').value;
        const container = document.getElementById('recipientSelection');
        
        if (type === 'single') {
            container.innerHTML = `
                <select id="user_id" name="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Sélectionnez un utilisateur</option>
                    ${users.map(user => `
                        <option value="${user.id}">${user.name} (${user.email})</option>
                    `).join('')}
                </select>
            `;
        } else if (type === 'all') {
            container.innerHTML = `
                <div class="p-3 bg-blue-50 text-blue-700 rounded-lg">
                    <i class="fas fa-users mr-2"></i>
                    <span>La notification sera envoyée à tous les utilisateurs (${users.length} utilisateurs)</span>
                </div>
            `;
        } else if (type === 'role') {
            container.innerHTML = `
                <select id="role" name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Sélectionnez un rôle</option>
                    <option value="admin">Administrateurs</option>
                    <option value="commercant">Commerçants</option>
                    <option value="user">Utilisateurs standards</option>
                </select>
            `;
        }
    }
    
    // Mettre à jour la prévisualisation
    function updatePreview() {
        const title = document.getElementById('title').value || 'Titre de la notification';
        const message = document.getElementById('message').value || 'Contenu du message...';
        const type = document.getElementById('type').value;
        
        const colors = {
            'info': 'bg-blue-100 text-blue-600',
            'success': 'bg-green-100 text-green-600',
            'warning': 'bg-yellow-100 text-yellow-600',
            'error': 'bg-red-100 text-red-600'
        };
        
        const icons = {
            'info': 'fa-info-circle',
            'success': 'fa-check-circle',
            'warning': 'fa-exclamation-triangle',
            'error': 'fa-times-circle'
        };
        
        const preview = document.getElementById('notificationPreview');
        preview.innerHTML = `
            <div class="p-4 border border-gray-200 rounded-lg">
                <div class="flex items-start space-x-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center ${colors[type]}">
                        <i class="fas ${icons[type]}"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">${title}</p>
                        <p class="text-sm text-gray-600 mt-1">${message}</p>
                        <p class="text-xs text-gray-400 mt-2">À l'instant</p>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Envoyer la notification
    async function sendNotification() {
        const form = document.getElementById('sendNotificationForm');
        const recipientType = document.getElementById('recipientType').value;
        
        // Préparer les données
        let data = {
            title: document.getElementById('title').value,
            message: document.getElementById('message').value,
            type: document.getElementById('type').value,
            icon: document.getElementById('icon').value || null
        };
        
        // Ajouter les destinataires
        if (recipientType === 'single') {
            const userId = document.getElementById('user_id').value;
            if (!userId) {
                showModal('Erreur', 'Veuillez sélectionner un utilisateur');
                return;
            }
            data.user_id = userId;
        } else if (recipientType === 'all') {
            data.send_to_all = true;
        } else if (recipientType === 'role') {
            const role = document.getElementById('role').value;
            if (!role) {
                showModal('Erreur', 'Veuillez sélectionner un rôle');
                return;
            }
            data.role = role;
        }
        
        // Validation
        if (!data.title || !data.message) {
            showModal('Erreur', 'Veuillez remplir tous les champs obligatoires');
            return;
        }
        
        // Afficher le modal de chargement
        document.getElementById('loadingModal').classList.remove('hidden');
        
        try {
            const response = await fetch('/api/notifications/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            document.getElementById('loadingModal').classList.add('hidden');
            
            if (result.success) {
                showModal('Succès', `${result.message} (${result.count} notification(s) envoyée(s))`);
                resetForm();
                loadNotificationStats();
                loadNotifications();
            } else {
                showModal('Erreur', result.message || 'Erreur lors de l\'envoi');
            }
        } catch (error) {
            document.getElementById('loadingModal').classList.add('hidden');
            showModal('Erreur', 'Une erreur est survenue');
            console.error('Erreur:', error);
        }
    }
    
    // Charger les statistiques
    async function loadNotificationStats() {
        try {
            const response = await fetch('/api/notifications/stats');
            const result = await response.json();
            
            if (result.success) {
                const stats = result.stats;
                const container = document.getElementById('notificationStats');
                
                container.innerHTML = `
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-sm text-gray-600">Total</p>
                            <p class="text-lg font-bold text-gray-900">${stats.total}</p>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-lg">
                            <p class="text-sm text-blue-600">Non lues</p>
                            <p class="text-lg font-bold text-blue-600">${stats.unread}</p>
                        </div>
                        <div class="bg-green-50 p-3 rounded-lg">
                            <p class="text-sm text-green-600">Aujourd'hui</p>
                            <p class="text-lg font-bold text-green-600">${stats.today}</p>
                        </div>
                        <div class="bg-purple-50 p-3 rounded-lg">
                            <p class="text-sm text-purple-600">Ce mois</p>
                            <p class="text-lg font-bold text-purple-600">${stats.this_month}</p>
                        </div>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Erreur chargement stats:', error);
        }
    }
    
    // Charger les notifications
    async function loadNotifications() {
        try {
            const response = await fetch('/api/notifications');
            const result = await response.json();
            
            if (result.success) {
                const container = document.getElementById('notificationsList');
                const notifications = result.notifications.data || result.notifications;
                
                if (notifications.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-8">
                            <i class="fas fa-bell-slash text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Aucune notification envoyée</p>
                        </div>
                    `;
                } else {
                    let html = `
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Destinataire</th>
                                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Titre</th>
                                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Type</th>
                                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Date</th>
                                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    notifications.forEach(notification => {
                        const colors = {
                            'info': 'bg-blue-100 text-blue-800',
                            'success': 'bg-green-100 text-green-800',
                            'warning': 'bg-yellow-100 text-yellow-800',
                            'error': 'bg-red-100 text-red-800'
                        };
                        
                        html += `
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4">
                                    ${notification.user ? notification.user.name : 'Utilisateur inconnu'}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-medium text-gray-900">${notification.title}</div>
                                    <div class="text-sm text-gray-500 truncate max-w-xs">${notification.message}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium ${colors[notification.type]}">
                                        ${notification.type === 'info' ? 'Info' : 
                                          notification.type === 'success' ? 'Succès' : 
                                          notification.type === 'warning' ? 'Avertissement' : 'Erreur'}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-gray-600">
                                    ${new Date(notification.created_at).toLocaleDateString('fr-FR')}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium ${notification.read ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800'}">
                                        ${notification.read ? 'Lu' : 'Non lu'}
                                    </span>
                                </td>
                            </tr>
                        `;
                    });
                    
                    html += `
                                </tbody>
                            </table>
                        </div>
                    `;
                    
                    container.innerHTML = html;
                }
            }
        } catch (error) {
            console.error('Erreur chargement notifications:', error);
        }
    }
    
    // Réinitialiser le formulaire
    function resetForm() {
        document.getElementById('sendNotificationForm').reset();
        updatePreview();
        updateRecipientSelection();
    }
    
    // Afficher un modal
    function showModal(title, message) {
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalMessage').textContent = message;
        document.getElementById('confirmationModal').classList.remove('hidden');
    }
    
    // Fermer le modal de confirmation
    function closeConfirmationModal() {
        document.getElementById('confirmationModal').class
            // Fermer le modal de confirmation
    function closeConfirmationModal() {
        document.getElementById('confirmationModal').classList.add('hidden');
    }
    
    // Fermer le modal de chargement
    function closeLoadingModal() {
        document.getElementById('loadingModal').classList.add('hidden');
    }
}
</script>
@endsection