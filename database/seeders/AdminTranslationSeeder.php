<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Translation;
use App\Models\Language;
use App\Models\Admin;

class AdminTranslationSeeder extends Seeder
{
    public function run()
    {
        $languages = Language::where('status', 'active')->get();
        $admin = Admin::first() ?? Admin::factory()->create(['id' => 1]);

        $translations = [
            // Dashboard
            'admin_dashboard' => [
                'en' => 'Admin Dashboard',
                'es' => 'Panel de Administración'
            ],
            'welcome_back' => [
                'en' => 'Welcome back',
                'es' => 'Bienvenido de vuelta'
            ],
            'last_login' => [
                'en' => 'Last login',
                'es' => 'Último acceso'
            ],
            'never' => [
                'en' => 'Never',
                'es' => 'Nunca'
            ],
            
            // Stats
            'total_users' => [
                'en' => 'Total Users',
                'es' => 'Usuarios Totales'
            ],
            'active_users' => [
                'en' => 'Active Users',
                'es' => 'Usuarios Activos'
            ],
            'total_revenue' => [
                'en' => 'Total Revenue',
                'es' => 'Ingresos Totales'
            ],
            'active_subscriptions' => [
                'en' => 'Active Subscriptions',
                'es' => 'Suscripciones Activas'
            ],
            
            // Quick Actions
            'quick_actions' => [
                'en' => 'Quick Actions',
                'es' => 'Acciones Rápidas'
            ],
            'add_user' => [
                'en' => 'Add User',
                'es' => 'Agregar Usuario'
            ],
            'upload_magazine' => [
                'en' => 'Upload Magazine',
                'es' => 'Subir Revista'
            ],
            'view_transactions' => [
                'en' => 'View Transactions',
                'es' => 'Ver Transacciones'
            ],
            'manage_users' => [
                'en' => 'Manage Users',
                'es' => 'Gestionar Usuarios'
            ],
            'referral_tree' => [
                'en' => 'Referral Tree',
                'es' => 'Árbol de Referidos'
            ],
            'commissions' => [
                'en' => 'Commissions',
                'es' => 'Comisiones'
            ],
            'payouts' => [
                'en' => 'Payouts',
                'es' => 'Pagos'
            ],
            'analytics_reports' => [
                'en' => 'Analytics & Reports',
                'es' => 'Análisis y Reportes'
            ],
            
            // Recent Activity
            'recent_transactions' => [
                'en' => 'Recent Transactions',
                'es' => 'Transacciones Recientes'
            ],
            'view_all_transactions' => [
                'en' => 'View all transactions',
                'es' => 'Ver todas las transacciones'
            ],
            'no_recent_transactions' => [
                'en' => 'No recent transactions',
                'es' => 'No hay transacciones recientes'
            ],
            'recent_activity' => [
                'en' => 'Recent Activity',
                'es' => 'Actividad Reciente'
            ],
            'no_recent_activity' => [
                'en' => 'No recent activity',
                'es' => 'No hay actividad reciente'
            ],
            
            // Navigation
            'dashboard' => [
                'en' => 'Dashboard',
                'es' => 'Panel de Control'
            ],
            'users' => [
                'en' => 'Users',
                'es' => 'Usuarios'
            ],
            'magazines' => [
                'en' => 'Magazines',
                'es' => 'Revistas'
            ],
            'transactions' => [
                'en' => 'Transactions',
                'es' => 'Transacciones'
            ],
            'subscriptions' => [
                'en' => 'Subscriptions',
                'es' => 'Suscripciones'
            ],
            'referrals' => [
                'en' => 'Referrals',
                'es' => 'Referidos'
            ],
            'commissions' => [
                'en' => 'Commissions',
                'es' => 'Comisiones'
            ],
            'payouts' => [
                'en' => 'Payouts',
                'es' => 'Pagos'
            ],
            'analytics' => [
                'en' => 'Analytics',
                'es' => 'Análisis'
            ],
            'settings' => [
                'en' => 'Settings',
                'es' => 'Configuración'
            ],
            'logout' => [
                'en' => 'Logout',
                'es' => 'Cerrar Sesión'
            ],
            'admin_login' => [
                'en' => 'Admin Login',
                'es' => 'Inicio de Sesión Admin'
            ]
        ];

        foreach ($translations as $key => $values) {
            foreach ($languages as $language) {
                $langCode = $language->code;
                if (isset($values[$langCode])) {
                    // Use updateOrCreate with the correct unique fields
                    Translation::updateOrCreate(
                        [
                            'language_code' => $langCode,
                            'key' => $key
                        ],
                        [
                            'value' => $values[$langCode],
                            'module' => 'admin',
                            'created_by_admin_id' => $admin->id,
                            'updated_by_admin_id' => $admin->id
                        ]
                    );
                }
            }
        }

        $this->command->info('Admin translations seeded successfully!');
    }
}
