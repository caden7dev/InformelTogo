@extends('layouts.admin')

@section('title', 'Envoyer une Notification')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Envoyer une Notification</h1>
        <p class="text-gray-600 mt-2">Envoyez une notification à un ou plusieurs utilisateurs</p>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6">
        <form id="sendNotificationForm" class="space-y-6">
            @csrf
            
            <!-- Destinataires -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Destinataires</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <select id="recipientType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="single">Utilisateur spécifique</option>
                            <option value="all">Tous les utilisateurs</option>
                            <option value="role">Par rôle</option>
                            <option value="multiple">Plusieurs utilisateurs</option>
                        </select>
                    </div>
                    
                    <div id="userSelectContainer">
                        <select id="user_id" name="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Sélectionnez un utilisateur</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Titre et Message -->
            <div class="grid grid-cols-1 gap-6">
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Options</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" id="urgent" name="urgent" class="mr-2">
                            <span class="text-sm text-gray-700">Notification urgente</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="send_email" name="send_email" class="mr-2">
                            <span class="text-sm text-gray-700">Envoyer par email également</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Boutons -->
            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Annuler
                </a>
                <button type="button" onclick="sendNotification()" 
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Envoyer la notification
                </button>
            </div>
        </form>
    </div>

    <!-- Historique des notifications -->
    <div class="mt-8 bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Notifications Récentes</h2>
        
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
                    @forelse($recentNotifications as $notification)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4">
                            {{ $notification->user->name }}
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-medium text-gray-900">{{ $notification->title }}</div>
                            <div class="text-sm text-gray-500 truncate max-w-xs">{{ $notification->message }}</div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium 
                                {{ $notification->type == 'info' ? 'bg-blue-100 text-blue-800' : 
                                   ($notification->type == 'success' ? 'bg-green-100 text-green-800' : 
                                   ($notification->type == 'warning' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                {{ ucfirst($notification->type) }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-gray-600">
                            {{ $notification->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 rounded-full text-xs font-medium 
                                {{ $notification->read ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800' }}">
                                {{ $notification->read ? 'Lu' : 'Non lu' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-4 px-4 text-center text-gray-500">
                            Aucune notification envoyée
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Gérer le changement de type de destinataire
    document.getElementById('recipientType').addEventListener('change', function() {
        const container = document.getElementById('userSelectContainer');
        const value = this.value;
        
        if (value === 'all') {
            container.innerHTML = `
                <div class="p-3 bg-blue-50 text-blue-700 rounded-lg">
                    <i class="fas fa-users mr-2"></i>
                    <span>La notification sera envoyée à tous les utilisateurs</span>
                </div>
            `;
        } else if (value === 'single') {
            container.innerHTML = `
                <select id="user_id" name="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Sélectionnez un utilisateur</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            `;
        } else if (value === 'role') {
            container.innerHTML = `
                <select id="role" name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Sélectionnez un rôle</option>
                    <option value="admin">Administrateurs</option>
                    <option value="commercant">Commerçants</option>
                    <option value="user">Utilisateurs standards</option>
                </select>
            `;
        }
    });

    async function sendNotification() {
        const form = document.getElementById('sendNotificationForm');
        const formData = new FormData(form);
        const recipientType = document.getElementById('recipientType').value;
        
        // Préparer les données selon le type de destinataire
        let data = {
            title: document.getElementById('title').value,
            message: document.getElementById('message').value,
            type: document.getElementById('type').value,
            metadata: {
                urgent: document.getElementById('urgent').checked,
                send_email: document.getElementById('send_email').checked
            }
        };
        
        if (recipientType === 'single') {
            const userId = document.getElementById('user_id').value;
            if (!userId) {
                alert('Veuillez sélectionner un utilisateur');
                return;
            }
            data.user_id = userId;
        } else if (recipientType === 'all') {
            data.send_to_all = true;
        } else if (recipientType === 'role') {
            const role = document.getElementById('role').value;
            if (!role) {
                alert('Veuillez sélectionner un rôle');
                return;
            }
            data.role = role;
        }
        
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
            
            if (result.success) {
                alert('Notification envoyée avec succès !');
                form.reset();
                window.location.reload(); // Recharger pour voir l'historique
            } else {
                alert('Erreur lors de l\'envoi de la notification');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur lors de l\'envoi de la notification');
        }
    }
</script>
@endsection