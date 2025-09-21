<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing users with new referral code format
        $users = User::whereNotNull('referral_code')->get();
        
        foreach ($users as $user) {
            // Check if user already has new format
            if (User::isValidReferralCodeFormat($user->referral_code)) {
                continue; // Skip if already in new format
            }
            
            // Generate new format referral code
            $newReferralCode = $this->generateNewFormatReferralCode($user);
            $user->update(['referral_code' => $newReferralCode]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not easily reversible as we don't store the old format
        // Users would need to regenerate their referral codes
        \Log::warning('Migration down() called for update_existing_users_with_new_referral_code_format - this is not easily reversible');
    }

    /**
     * Generate a new format referral code for a user
     */
    private function generateNewFormatReferralCode(User $user): string
    {
        // Clean username for code generation
        $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $user->name));
        $username = substr($username, 0, 6); // Limit to 6 characters for better pattern
        
        // If username is too short, use first 3 characters of email
        if (strlen($username) < 3) {
            $emailParts = explode('@', $user->email);
            $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $emailParts[0]));
            $username = substr($username, 0, 6);
        }
        
        // Ensure minimum length
        if (strlen($username) < 3) {
            $username = 'USER' . substr($user->id, 0, 3);
        }
        
        $username = strtoupper($username);
        
        // Generate code with pattern: REF-{USERNAME}-{RANDOM}
        do {
            $randomSuffix = strtoupper(substr(md5($user->id . time() . rand()), 0, 4));
            $code = "REF-{$username}-{$randomSuffix}";
        } while ($this->referralCodeExists($code));
        
        return $code;
    }

    /**
     * Check if referral code already exists
     */
    private function referralCodeExists(string $code): bool
    {
        return User::where('referral_code', $code)->exists();
    }
};