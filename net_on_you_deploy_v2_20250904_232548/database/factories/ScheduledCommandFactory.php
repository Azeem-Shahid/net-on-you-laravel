<?php

namespace Database\Factories;

use App\Models\ScheduledCommand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ScheduledCommandFactory extends Factory
{
    protected $model = ScheduledCommand::class;

    public function definition()
    {
        $frequencies = ['manual', 'daily', 'weekly', 'monthly'];
        $statuses = ['active', 'inactive'];
        
        return [
            'command' => $this->faker->randomElement([
                'system:cleanup',
                'system:health-check',
                'system:backup-database',
                'subscriptions:check-expiry',
                'commissions:check-eligibility'
            ]),
            'frequency' => $this->faker->randomElement($frequencies),
            'next_run_at' => $this->faker->randomElement([
                null,
                Carbon::now()->addHour(),
                Carbon::now()->addDay(),
                Carbon::now()->addWeek()
            ]),
            'status' => $this->faker->randomElement($statuses),
            'description' => $this->faker->sentence(),
        ];
    }

    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active',
                'next_run_at' => Carbon::now()->addHour(),
            ];
        });
    }

    public function daily()
    {
        return $this->state(function (array $attributes) {
            return [
                'frequency' => 'daily',
                'status' => 'active',
                'next_run_at' => Carbon::tomorrow()->setTime(6, 0, 0),
            ];
        });
    }

    public function weekly()
    {
        return $this->state(function (array $attributes) {
            return [
                'frequency' => 'weekly',
                'status' => 'active',
                'next_run_at' => Carbon::now()->addWeek()->startOfWeek()->addDays(1)->setTime(6, 0, 0),
            ];
        });
    }

    public function monthly()
    {
        return $this->state(function (array $attributes) {
            return [
                'frequency' => 'monthly',
                'status' => 'active',
                'next_run_at' => Carbon::now()->addMonth()->startOfMonth()->addDays(1)->setTime(6, 0, 0),
            ];
        });
    }

    public function due()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active',
                'next_run_at' => Carbon::now()->subHour(),
            ];
        });
    }
}
