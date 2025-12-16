<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CommercantController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\SettingController;

/*
|--------------------------------------------------------------------------
| Routes Publiques
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

/*
|--------------------------------------------------------------------------
| Routes pour Invités (Non connectés)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

/*
|--------------------------------------------------------------------------
| Routes Protégées (Connectés uniquement)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    
    // ==================== DASHBOARD PRINCIPAL ====================
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // API Dashboard
    Route::prefix('api/dashboard')->name('api.dashboard.')->group(function () {
        Route::get('/quick-stats', [DashboardController::class, 'quickStats'])->name('quick-stats');
        Route::get('/chart-data', [DashboardController::class, 'chartData'])->name('chart-data');
        Route::get('/monthly-stats', [DashboardController::class, 'monthlyStats'])->name('monthly-stats');
        Route::post('/filter', [DashboardController::class, 'filter'])->name('filter');
        Route::get('/recent-transactions', [DashboardController::class, 'recentTransactions'])->name('recent-transactions');
        Route::get('/expense-categories', [DashboardController::class, 'expenseCategories'])->name('expense-categories');
        Route::get('/goals-progress', [DashboardController::class, 'goalsProgress'])->name('goals-progress');
    });

    // ==================== AIDE & SUPPORT ====================
    
    Route::prefix('help')->name('help.')->group(function () {
        Route::get('/support', [HelpController::class, 'support'])->name('support');
        Route::get('/faq', [HelpController::class, 'faq'])->name('faq');
        Route::get('/contact', [HelpController::class, 'contact'])->name('contact');
        Route::post('/contact', [HelpController::class, 'sendContact'])->name('contact.send');
        Route::get('/documentation', [HelpController::class, 'documentation'])->name('documentation');
    });

    // ==================== TRANSACTIONS STANDARD ====================
    
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/create', [TransactionController::class, 'create'])->name('create');
        Route::post('/', [TransactionController::class, 'store'])->name('store');
        Route::get('/{transaction}', [TransactionController::class, 'show'])->name('show');
        Route::get('/{transaction}/edit', [TransactionController::class, 'edit'])->name('edit');
        Route::put('/{transaction}', [TransactionController::class, 'update'])->name('update');
        Route::delete('/{transaction}', [TransactionController::class, 'destroy'])->name('destroy');
        
        // Routes supplémentaires
        Route::get('/type/{type}', [TransactionController::class, 'byType'])
            ->name('byType')
            ->where('type', 'income|expense');
        Route::get('/category/{category}', [TransactionController::class, 'byCategory'])
            ->name('byCategory');
        Route::post('/filter', [TransactionController::class, 'filter'])
            ->name('filter');
        Route::get('/export', [ExportController::class, 'transactions'])
            ->name('export');
        Route::post('/import', [TransactionController::class, 'import'])
            ->name('import');
        Route::get('/stats', [TransactionController::class, 'stats'])
            ->name('stats');
        Route::get('/recent', [TransactionController::class, 'recent'])
            ->name('recent');
        
        // API Transactions
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/', [TransactionController::class, 'apiIndex'])->name('index');
            Route::get('/recent', [TransactionController::class, 'apiRecent'])->name('recent');
            Route::get('/stats', [TransactionController::class, 'apiStats'])->name('stats');
        });
    });

    // ==================== CATÉGORIES ====================
    
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{category}', [CategoryController::class, 'show'])->name('show');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::get('/type/{type}', [CategoryController::class, 'byType'])->name('byType');
    });

    // ==================== BUDGETS ====================
    
    Route::prefix('budgets')->name('budgets.')->group(function () {
        Route::get('/', [BudgetController::class, 'index'])->name('index');
        Route::get('/create', [BudgetController::class, 'create'])->name('create');
        Route::post('/', [BudgetController::class, 'store'])->name('store');
        Route::get('/{budget}', [BudgetController::class, 'show'])->name('show');
        Route::get('/{budget}/edit', [BudgetController::class, 'edit'])->name('edit');
        Route::put('/{budget}', [BudgetController::class, 'update'])->name('update');
        Route::delete('/{budget}', [BudgetController::class, 'destroy'])->name('destroy');
        Route::get('/{budget}/progress', [BudgetController::class, 'progress'])->name('progress');
    });

    // ==================== OBJECTIFS ====================
    
    Route::prefix('goals')->name('goals.')->group(function () {
        Route::get('/', [GoalController::class, 'index'])->name('index');
        Route::get('/create', [GoalController::class, 'create'])->name('create');
        Route::post('/', [GoalController::class, 'store'])->name('store');
        Route::get('/{goal}', [GoalController::class, 'show'])->name('show');
        Route::get('/{goal}/edit', [GoalController::class, 'edit'])->name('edit');
        Route::put('/{goal}', [GoalController::class, 'update'])->name('update');
        Route::delete('/{goal}', [GoalController::class, 'destroy'])->name('destroy');
        Route::get('/{goal}/progress', [GoalController::class, 'progress'])->name('progress');
    });

    // ==================== RAPPORTS ====================
    
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
        Route::get('/categorical', [ReportController::class, 'categorical'])->name('categorical');
        Route::get('/monthly', [ReportController::class, 'monthly'])->name('monthly');
        Route::get('/budget', [ReportController::class, 'budget'])->name('budget');
        Route::get('/custom', [ReportController::class, 'custom'])->name('custom');
        Route::post('/custom/generate', [ReportController::class, 'generateCustom'])->name('custom.generate');
        Route::resource('budgets', BudgetController::class);
    });

    // ==================== EXPORT ====================
    
    Route::prefix('export')->name('export.')->group(function () {
        Route::get('/transactions/csv', [ExportController::class, 'transactionsCSV'])->name('transactions.csv');
        Route::get('/transactions/excel', [ExportController::class, 'transactionsExcel'])->name('transactions.excel');
        Route::get('/transactions/pdf', [ExportController::class, 'transactionsPDF'])->name('transactions.pdf');
        Route::get('/reports/pdf', [ExportController::class, 'reportsPDF'])->name('reports.pdf');
        Route::get('/budget/pdf', [ExportController::class, 'budgetPDF'])->name('budget.pdf');
    });

    // ==================== ALERTES ====================
    
    Route::prefix('alerts')->name('alerts.')->group(function () {
        Route::get('/', [AlertController::class, 'index'])->name('index');
        Route::get('/create', [AlertController::class, 'create'])->name('create');
        Route::post('/', [AlertController::class, 'store'])->name('store');
        Route::get('/{alert}', [AlertController::class, 'show'])->name('show');
        Route::get('/{alert}/edit', [AlertController::class, 'edit'])->name('edit');
        Route::put('/{alert}', [AlertController::class, 'update'])->name('update');
        Route::delete('/{alert}', [AlertController::class, 'destroy'])->name('destroy');
        Route::post('/{alert}/test', [AlertController::class, 'test'])->name('test');
    });

    // ==================== PROFIL & PARAMÈTRES ====================
    
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::put('/update-password', [ProfileController::class, 'updatePassword'])->name('update-password');
        Route::delete('/destroy', [ProfileController::class, 'destroy'])->name('destroy');
        Route::get('/settings', [SettingController::class, 'index'])->name('settings');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::get('/preferences', [SettingController::class, 'preferences'])->name('preferences');
        Route::put('/preferences', [SettingController::class, 'updatePreferences'])->name('preferences.update');
        Route::get('/notifications', [SettingController::class, 'notifications'])->name('notifications');
        Route::put('/notifications', [SettingController::class, 'updateNotifications'])->name('notifications.update');
    });

    // ==================== NOTIFICATIONS ====================
    
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/clear-all', [NotificationController::class, 'clearAll'])->name('clear-all');
    });

    // ==================== NOTIFICATIONS API ====================
    
    Route::prefix('api/notifications')->name('api.notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'apiIndex'])->name('index');
        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::post('/{notification}/read', [NotificationController::class, 'apiMarkAsRead'])->name('read');
        Route::post('/mark-all-read', [NotificationController::class, 'apiMarkAllAsRead'])->name('mark-all-read');
        Route::get('/types', [NotificationController::class, 'types'])->name('types');
        Route::post('/test', [NotificationController::class, 'test'])->name('test');
    });

    // ==================== ROUTES COMMERÇANT ====================
    
    Route::middleware(['commercant'])->prefix('commercant')->name('commercant.')->group(function () {
        // Dashboard commerçant
        Route::get('/dashboard', [CommercantController::class, 'dashboard'])->name('dashboard');
        
        // Transactions du commerçant
        Route::prefix('transactions')->name('transactions.')->group(function () {
            Route::get('/', [CommercantController::class, 'transactions'])->name('index');
            Route::get('/create', [CommercantController::class, 'createTransaction'])->name('create');
            Route::post('/', [CommercantController::class, 'storeTransaction'])->name('store');
            Route::get('/{transaction}', [CommercantController::class, 'showTransaction'])->name('show');
            Route::get('/{transaction}/edit', [CommercantController::class, 'editTransaction'])->name('edit');
            Route::put('/{transaction}', [CommercantController::class, 'updateTransaction'])->name('update');
            Route::delete('/{transaction}', [CommercantController::class, 'destroyTransaction'])->name('destroy');
            
            // Export
            Route::get('/export/csv', [CommercantController::class, 'exportTransactions'])->name('export.csv');
        });
        
        // Statistiques commerçant
        Route::get('/stats', [CommercantController::class, 'stats'])->name('stats');
        
        // Rapports commerçant
        Route::get('/reports', [CommercantController::class, 'reports'])->name('reports');
        
        // API Commerçant
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/quick-stats', [CommercantController::class, 'quickStats'])->name('quick-stats');
            Route::get('/dashboard', [CommercantController::class, 'apiDashboard'])->name('dashboard');
        });
    });

    // ==================== ADMIN ROUTES ====================
    
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        // Dashboard Admin
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Gestion des utilisateurs
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminController::class, 'users'])->name('index');
            Route::get('/create', [AdminController::class, 'createUser'])->name('create');
            Route::post('/', [AdminController::class, 'storeUser'])->name('store');
            Route::get('/{user}', [AdminController::class, 'showUser'])->name('show');
            Route::get('/{user}/edit', [AdminController::class, 'editUser'])->name('edit');
            Route::put('/{user}', [AdminController::class, 'updateUser'])->name('update');
            Route::delete('/{user}', [AdminController::class, 'destroyUser'])->name('destroy');
            Route::post('/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('toggle-status');
            Route::post('/{user}/reset-password', [AdminController::class, 'resetPassword'])->name('reset-password');
        });
        
        // Notifications Admin
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/send', [AdminController::class, 'sendNotification'])->name('send');
            Route::post('/send', [AdminController::class, 'storeNotification'])->name('store');
            Route::get('/history', [AdminController::class, 'notificationHistory'])->name('history');
            Route::get('/templates', [AdminController::class, 'notificationTemplates'])->name('templates');
            Route::post('/templates', [AdminController::class, 'storeTemplate'])->name('templates.store');
            Route::delete('/templates/{template}', [AdminController::class, 'destroyTemplate'])->name('templates.destroy');
        });
        
        // Transactions globales
        Route::prefix('transactions')->name('transactions.')->group(function () {
            Route::get('/', [AdminController::class, 'allTransactions'])->name('index');
            Route::get('/create', [AdminController::class, 'createTransaction'])->name('create');
            Route::post('/', [AdminController::class, 'storeTransaction'])->name('store');
            Route::get('/{transaction}', [AdminController::class, 'showTransaction'])->name('show');
            Route::get('/{transaction}/edit', [AdminController::class, 'editTransaction'])->name('edit');
            Route::put('/{transaction}', [AdminController::class, 'updateTransaction'])->name('update');
            Route::delete('/{transaction}', [AdminController::class, 'destroyTransaction'])->name('destroy');
            Route::post('/bulk-actions', [AdminController::class, 'bulkActions'])->name('bulk-actions');
        });
        
        // Statistiques admin
        Route::get('/stats', [AdminController::class, 'stats'])->name('stats');
        
        // Rapports admin
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [AdminController::class, 'reports'])->name('index');
            Route::get('/system', [AdminController::class, 'systemReport'])->name('system');
            Route::get('/users', [AdminController::class, 'usersReport'])->name('users');
            Route::get('/transactions', [AdminController::class, 'transactionsReport'])->name('transactions');
        });
        
        // Système et paramètres
        Route::prefix('system')->name('system.')->group(function () {
            Route::get('/settings', [AdminController::class, 'systemSettings'])->name('settings');
            Route::put('/settings', [AdminController::class, 'updateSystemSettings'])->name('settings.update');
            Route::get('/logs', [AdminController::class, 'systemLogs'])->name('logs');
            Route::get('/backup', [AdminController::class, 'backup'])->name('backup');
            Route::post('/backup/create', [AdminController::class, 'createBackup'])->name('backup.create');
        });
        
        // API Admin
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/stats', [AdminController::class, 'apiStats'])->name('stats');
            Route::get('/transactions', [AdminController::class, 'apiTransactions'])->name('transactions');
            Route::get('/users', [AdminController::class, 'apiUsers'])->name('users');
            Route::get('/notifications', [AdminController::class, 'apiNotifications'])->name('notifications');
            Route::get('/system-health', [AdminController::class, 'systemHealth'])->name('system-health');
        });
    });

    // ==================== DÉCONNEXION ====================
    
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Routes API pour applications externes (Sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->prefix('api/v1')->name('api.v1.')->group(function () {
    // Dashboard API
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/stats', [DashboardController::class, 'apiV1Stats'])->name('stats');
        Route::get('/transactions', [DashboardController::class, 'apiV1Transactions'])->name('transactions');
        Route::get('/recent', [DashboardController::class, 'apiV1Recent'])->name('recent');
    });
    
    // Transactions API
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'apiV1Index'])->name('index');
        Route::get('/recent', [TransactionController::class, 'apiV1Recent'])->name('recent');
        Route::get('/stats', [TransactionController::class, 'apiV1Stats'])->name('stats');
        Route::post('/', [TransactionController::class, 'apiV1Store'])->name('store');
        Route::get('/{transaction}', [TransactionController::class, 'apiV1Show'])->name('show');
        Route::put('/{transaction}', [TransactionController::class, 'apiV1Update'])->name('update');
        Route::delete('/{transaction}', [TransactionController::class, 'apiV1Destroy'])->name('destroy');
    });
    
    // Notifications API
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'apiV1Index'])->name('index');
        Route::get('/unread-count', [NotificationController::class, 'apiV1UnreadCount'])->name('unread-count');
        Route::post('/{notification}/read', [NotificationController::class, 'apiV1MarkAsRead'])->name('read');
        Route::post('/mark-all-read', [NotificationController::class, 'apiV1MarkAllAsRead'])->name('mark-all-read');
    });
    
    // Admin API
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/stats', [AdminController::class, 'apiV1Stats'])->name('stats');
        Route::get('/transactions', [AdminController::class, 'apiV1Transactions'])->name('transactions');
        Route::get('/users', [AdminController::class, 'apiV1Users'])->name('users');
    });
    
    // Commerçant API
    Route::middleware(['commercant'])->prefix('commercant')->name('commercant.')->group(function () {
        Route::get('/quick-stats', [CommercantController::class, 'apiV1QuickStats'])->name('quick-stats');
        Route::get('/dashboard', [CommercantController::class, 'apiV1Dashboard'])->name('dashboard');
        Route::get('/transactions', [CommercantController::class, 'apiV1Transactions'])->name('transactions');
    });
});

/*
|--------------------------------------------------------------------------
| WebSocket pour notifications temps réel
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('ws')->name('ws.')->group(function () {
    Route::get('/notifications/connect', [NotificationController::class, 'websocketConnect'])->name('notifications.connect');
    Route::post('/notifications/broadcast', [NotificationController::class, 'websocketBroadcast'])->name('notifications.broadcast');
});

/*
|--------------------------------------------------------------------------
| Redirections utiles
|--------------------------------------------------------------------------
*/

Route::get('/home', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif (in_array($role, ['commercant', 'merchant'])) {
            return redirect()->route('commercant.dashboard');
        } else {
            return redirect()->route('dashboard');
        }
    }
    return redirect()->route('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Fallback Route (404)
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif (in_array($role, ['commercant', 'merchant'])) {
            return redirect()->route('commercant.dashboard');
        } else {
            return redirect()->route('dashboard');
        }
    }
    return redirect()->route('welcome');
});