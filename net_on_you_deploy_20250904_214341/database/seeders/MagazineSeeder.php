<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Magazine;
use App\Models\Admin;

class MagazineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first admin for uploaded_by_admin_id
        $admin = Admin::first();
        if (!$admin) {
            $admin = Admin::create([
                'name' => 'Default Admin',
                'email' => 'default@example.com',
                'password' => bcrypt('admin123'),
                'role' => 'super_admin',
                'status' => 'active',
            ]);
        }

        // Technology magazines
        $techMagazines = [
            [
                'title' => 'Tech Trends 2024',
                'description' => 'Latest technology trends and innovations in 2024',
                'category' => 'Technology',
                'language_code' => 'en',
                'file_path' => 'magazines/tech-trends-2024.pdf',
                'file_name' => 'tech-trends-2024.pdf',
                'file_size' => 2048000,
                'mime_type' => 'application/pdf',
                'status' => 'active',
                'uploaded_by_admin_id' => $admin->id,
                'published_at' => now(),
            ],
            [
                'title' => 'AI Revolution',
                'description' => 'Artificial Intelligence and its impact on society',
                'category' => 'Technology',
                'language_code' => 'en',
                'file_path' => 'magazines/ai-revolution.pdf',
                'file_name' => 'ai-revolution.pdf',
                'file_size' => 1536000,
                'mime_type' => 'application/pdf',
                'status' => 'active',
                'uploaded_by_admin_id' => $admin->id,
                'published_at' => now(),
            ],
            [
                'title' => 'Blockchain Basics',
                'description' => 'Understanding blockchain technology and cryptocurrencies',
                'category' => 'Technology',
                'language_code' => 'en',
                'file_path' => 'magazines/blockchain-basics.pdf',
                'file_name' => 'blockchain-basics.pdf',
                'file_size' => 1792000,
                'mime_type' => 'application/pdf',
                'status' => 'active',
                'uploaded_by_admin_id' => $admin->id,
                'published_at' => now(),
            ],
        ];

        // Business magazines
        $businessMagazines = [
            [
                'title' => 'Startup Success',
                'description' => 'Guide to building successful startups',
                'category' => 'Business',
                'language_code' => 'en',
                'file_path' => 'magazines/startup-success.pdf',
                'file_name' => 'startup-success.pdf',
                'file_size' => 2560000,
                'mime_type' => 'application/pdf',
                'status' => 'active',
                'uploaded_by_admin_id' => $admin->id,
                'published_at' => now(),
            ],
            [
                'title' => 'Digital Marketing',
                'description' => 'Modern digital marketing strategies',
                'category' => 'Business',
                'language_code' => 'en',
                'file_path' => 'magazines/digital-marketing.pdf',
                'file_name' => 'digital-marketing.pdf',
                'file_size' => 1920000,
                'mime_type' => 'application/pdf',
                'status' => 'active',
                'uploaded_by_admin_id' => $admin->id,
                'published_at' => now(),
            ],
        ];

        // Spanish magazines
        $spanishMagazines = [
            [
                'title' => 'Tecnología Moderna',
                'description' => 'Tendencias tecnológicas modernas en español',
                'category' => 'Technology',
                'language_code' => 'es',
                'file_path' => 'magazines/tecnologia-moderna.pdf',
                'file_name' => 'tecnologia-moderna.pdf',
                'file_size' => 1280000,
                'mime_type' => 'application/pdf',
                'status' => 'active',
                'uploaded_by_admin_id' => $admin->id,
                'published_at' => now(),
            ],
            [
                'title' => 'Negocios Digitales',
                'description' => 'Estrategias de negocios en la era digital',
                'category' => 'Business',
                'language_code' => 'es',
                'file_path' => 'magazines/negocios-digitales.pdf',
                'file_name' => 'negocios-digitales.pdf',
                'file_size' => 1664000,
                'mime_type' => 'application/pdf',
                'status' => 'active',
                'uploaded_by_admin_id' => $admin->id,
                'published_at' => now(),
            ],
        ];

        // French magazines
        $frenchMagazines = [
            [
                'title' => 'Innovation Technologique',
                'description' => 'Innovations technologiques en français',
                'category' => 'Technology',
                'language_code' => 'fr',
                'file_path' => 'magazines/innovation-technologique.pdf',
                'file_name' => 'innovation-technologique.pdf',
                'file_size' => 1408000,
                'mime_type' => 'application/pdf',
                'status' => 'active',
                'uploaded_by_admin_id' => $admin->id,
                'published_at' => now(),
            ],
        ];

        // German magazines
        $germanMagazines = [
            [
                'title' => 'Digitale Wirtschaft',
                'description' => 'Digitale Wirtschaft und Innovationen auf Deutsch',
                'category' => 'Business',
                'language_code' => 'de',
                'file_path' => 'magazines/digitale-wirtschaft.pdf',
                'file_name' => 'digitale-wirtschaft.pdf',
                'file_size' => 1152000,
                'mime_type' => 'application/pdf',
                'status' => 'active',
                'uploaded_by_admin_id' => $admin->id,
                'published_at' => now(),
            ],
        ];

        // Draft magazines (using inactive status)
        $draftMagazines = [
            [
                'title' => 'Future of Work',
                'description' => 'The future of work in the digital age (Draft)',
                'category' => 'Business',
                'language_code' => 'en',
                'file_path' => 'magazines/future-of-work.pdf',
                'file_name' => 'future-of-work.pdf',
                'file_size' => 896000,
                'mime_type' => 'application/pdf',
                'status' => 'inactive',
                'uploaded_by_admin_id' => $admin->id,
                'published_at' => null,
            ],
        ];

        // Inactive magazines
        $inactiveMagazines = [
            [
                'title' => 'Legacy Systems',
                'description' => 'Managing legacy systems in modern IT (Inactive)',
                'category' => 'Technology',
                'language_code' => 'en',
                'file_path' => 'magazines/legacy-systems.pdf',
                'file_name' => 'legacy-systems.pdf',
                'file_size' => 1024000,
                'mime_type' => 'application/pdf',
                'status' => 'inactive',
                'uploaded_by_admin_id' => $admin->id,
                'published_at' => now()->subMonths(6),
            ],
        ];

        // Combine all magazines
        $allMagazines = array_merge(
            $techMagazines,
            $businessMagazines,
            $spanishMagazines,
            $frenchMagazines,
            $germanMagazines,
            $draftMagazines,
            $inactiveMagazines
        );

        // Create magazines
        foreach ($allMagazines as $magazineData) {
            Magazine::create($magazineData);
        }

        $this->command->info('Magazines seeded successfully!');
        $this->command->info('Created ' . count($allMagazines) . ' magazines in various categories and languages.');
        $this->command->info('Categories: Technology, Business');
        $this->command->info('Languages: English, Spanish, French, German');
        $this->command->info('Statuses: Active, Inactive, Archived');
    }
}
