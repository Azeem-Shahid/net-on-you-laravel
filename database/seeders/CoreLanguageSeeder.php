<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;

class CoreLanguageSeeder extends Seeder
{
    /**
     * Seed the core languages for the system
     */
    public function run(): void
    {
        $this->command->info('Seeding core languages...');

        $coreLanguages = [
            [
                'code' => 'en',
                'name' => 'English',
                'is_default' => true,
                'status' => 'active',
                'is_core' => true,
            ],
            [
                'code' => 'es',
                'name' => 'Español',
                'is_default' => false,
                'status' => 'active',
                'is_core' => true,
            ],
            [
                'code' => 'pt',
                'name' => 'Português',
                'is_default' => false,
                'status' => 'active',
                'is_core' => true,
            ],
            [
                'code' => 'it',
                'name' => 'Italiano',
                'is_default' => false,
                'status' => 'active',
                'is_core' => true,
            ],
        ];

        foreach ($coreLanguages as $languageData) {
            Language::updateOrCreate(
                ['code' => $languageData['code']],
                $languageData
            );
            
            $this->command->info("✓ Language {$languageData['name']} ({$languageData['code']}) seeded");
        }

        $this->command->info('Core languages seeded successfully!');
    }
}

