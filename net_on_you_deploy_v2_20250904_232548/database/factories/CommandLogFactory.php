<?php

namespace Database\Factories;

use App\Models\CommandLog;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class CommandLogFactory extends Factory
{
    protected $model = CommandLog::class;

    public function definition()
    {
        $statuses = ['success', 'failed'];
        
        return [
            'command' => $this->faker->randomElement([
                'system:cleanup',
                'system:health-check',
                'system:backup-database',
                'subscriptions:check-expiry',
                'commissions:check-eligibility'
            ]),
            'output' => $this->faker->randomElement([
                'Command executed successfully',
                'Cleanup completed. Removed 15 old files.',
                'Health check passed. All systems operational.',
                'Database backup created successfully.',
                'Subscription check completed. Found 3 expired subscriptions.'
            ]),
            'status' => $this->faker->randomElement($statuses),
            'executed_by_admin_id' => $this->faker->optional(0.7)->randomElement(Admin::pluck('id')->toArray()),
            'executed_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'error_message' => function (array $attributes) {
                return $attributes['status'] === 'failed' 
                    ? $this->faker->sentence() 
                    : null;
            },
            'execution_time_ms' => $this->faker->numberBetween(50, 5000),
        ];
    }

    public function successful()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'success',
                'error_message' => null,
            ];
        });
    }

    public function failed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'failed',
                'output' => null,
                'error_message' => $this->faker->sentence(),
            ];
        });
    }

    public function recent()
    {
        return $this->state(function (array $attributes) {
            return [
                'executed_at' => $this->faker->dateTimeBetween('-1 day', 'now'),
            ];
        });
    }

    public function fast()
    {
        return $this->state(function (array $attributes) {
            return [
                'execution_time_ms' => $this->faker->numberBetween(10, 100),
            ];
        });
    }

    public function slow()
    {
        return $this->state(function (array $attributes) {
            return [
                'execution_time_ms' => $this->faker->numberBetween(5000, 30000),
            ];
        });
    }
}
