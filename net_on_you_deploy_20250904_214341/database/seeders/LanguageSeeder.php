<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;
use App\Models\Translation;
use App\Models\Admin;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have an admin user for created_by_admin_id
        $admin = Admin::first();
        if (!$admin) {
            $admin = Admin::create([
                'name' => 'System Admin',
                'email' => 'admin@netonyou.com',
                'password' => bcrypt('Admin@NetOnYou2024!'),
                'role' => 'super_admin',
                'status' => 'active'
            ]);
        }

        // Create languages
        $languages = [
            [
                'code' => 'en',
                'name' => 'English',
                'is_default' => true,
                'status' => 'active'
            ],
            [
                'code' => 'es',
                'name' => 'Español',
                'is_default' => false,
                'status' => 'active'
            ]
        ];

        foreach ($languages as $langData) {
            Language::updateOrCreate(
                ['code' => $langData['code']],
                $langData
            );
        }

        // Sample translations for different modules
        $translations = [
            // Auth Module
            'auth' => [
                'login' => [
                    'en' => 'Login',
                    'es' => 'Iniciar Sesión'
                ],
                'register' => [
                    'en' => 'Register',
                    'es' => 'Registrarse'
                ],
                'email' => [
                    'en' => 'Email',
                    'es' => 'Correo Electrónico'
                ],
                'password' => [
                    'en' => 'Password',
                    'es' => 'Contraseña'
                ],
                'remember_me' => [
                    'en' => 'Remember me',
                    'es' => 'Recordarme'
                ],
                'forgot_password' => [
                    'en' => 'Forgot your password?',
                    'es' => '¿Olvidaste tu contraseña?'
                ],
                'logout' => [
                    'en' => 'Logout',
                    'es' => 'Cerrar Sesión'
                ]
            ],
            // Dashboard Module
            'dashboard' => [
                'welcome' => [
                    'en' => 'Welcome to your dashboard',
                    'es' => 'Bienvenido a tu panel de control'
                ],
                'overview' => [
                    'en' => 'Overview',
                    'es' => 'Resumen'
                ],
                'recent_activity' => [
                    'en' => 'Recent Activity',
                    'es' => 'Actividad Reciente'
                ],
                'quick_actions' => [
                    'en' => 'Quick Actions',
                    'es' => 'Acciones Rápidas'
                ]
            ],
            // Payment Module
            'payment' => [
                'checkout' => [
                    'en' => 'Checkout',
                    'es' => 'Pagar'
                ],
                'payment_history' => [
                    'en' => 'Payment History',
                    'es' => 'Historial de Pagos'
                ],
                'amount' => [
                    'en' => 'Amount',
                    'es' => 'Cantidad'
                ],
                'status' => [
                    'en' => 'Status',
                    'es' => 'Estado'
                ],
                'date' => [
                    'en' => 'Date',
                    'es' => 'Fecha'
                ],
                'pending' => [
                    'en' => 'Pending',
                    'es' => 'Pendiente'
                ],
                'completed' => [
                    'en' => 'Completed',
                    'es' => 'Completado'
                ],
                'failed' => [
                    'en' => 'Failed',
                    'es' => 'Fallido'
                ]
            ],
            // Common Module
            'common' => [
                'dashboard' => [
                    'en' => 'Dashboard',
                    'es' => 'Panel de Control'
                ],
                'magazines' => [
                    'en' => 'Magazines',
                    'es' => 'Revistas'
                ],
                'payments' => [
                    'en' => 'Payments',
                    'es' => 'Pagos'
                ],
                'profile' => [
                    'en' => 'Profile',
                    'es' => 'Perfil'
                ],
                'settings' => [
                    'en' => 'Settings',
                    'es' => 'Configuración'
                ],
                'save' => [
                    'en' => 'Save',
                    'es' => 'Guardar'
                ],
                'cancel' => [
                    'en' => 'Cancel',
                    'es' => 'Cancelar'
                ],
                'edit' => [
                    'en' => 'Edit',
                    'es' => 'Editar'
                ],
                'delete' => [
                    'en' => 'Delete',
                    'es' => 'Eliminar'
                ],
                'confirm' => [
                    'en' => 'Confirm',
                    'es' => 'Confirmar'
                ],
                'yes' => [
                    'en' => 'Yes',
                    'es' => 'Sí'
                ],
                'no' => [
                    'en' => 'No',
                    'es' => 'No'
                ],
                'loading' => [
                    'en' => 'Loading...',
                    'es' => 'Cargando...'
                ],
                'error' => [
                    'en' => 'Error',
                    'es' => 'Error'
                ],
                'success' => [
                    'en' => 'Success',
                    'es' => 'Éxito'
                ],
                'warning' => [
                    'en' => 'Warning',
                    'es' => 'Advertencia'
                ],
                'info' => [
                    'en' => 'Information',
                    'es' => 'Información'
                ]
            ]
        ];

        // Insert translations
        foreach ($translations as $module => $moduleTranslations) {
            foreach ($moduleTranslations as $key => $values) {
                foreach ($values as $langCode => $value) {
                    Translation::updateOrCreate(
                        [
                            'language_code' => $langCode,
                            'key' => $key,
                            'module' => $module
                        ],
                        [
                            'value' => $value,
                            'created_by_admin_id' => $admin->id,
                            'updated_by_admin_id' => $admin->id
                        ]
                    );
                }
            }
        }

        $this->command->info('Languages and translations seeded successfully!');
        $this->command->info('Available languages: ' . implode(', ', array_column($languages, 'name')));
    }
}
