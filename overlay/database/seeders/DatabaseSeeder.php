<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create(['name' => 'BugShield Admin', 'email' => 'admin@bugshield.bd', 'password' => 'Admin@12345',
            'role' => 'admin', 'status' => 'active']);

        // Demo approved organization with one program
        $orgUser = User::create(['name' => 'Demo Org Owner', 'email' => 'org@demo.bd', 'password' => 'password',
            'role' => 'organization', 'status' => 'active', 'phone' => '01711111111']);
        $org = Organization::create(['user_id' => $orgUser->id, 'name' => 'Demo e-Service Portal',
            'website' => 'https://demo.example.bd', 'trx_id' => 'DEMO000001', 'sender_number' => '01711111111',
            'fee_amount' => config('bugshield.org_fee'), 'status' => 'approved', 'approved_at' => now()]);
        $program = $org->programs()->create([
            'title' => 'Demo e-Service Portal VDP',
            'description' => 'Help us secure a citizen-facing e-service portal.',
            'scope' => "In scope: *.demo.example.bd\nOut of scope: denial-of-service, social engineering, physical attacks.",
        ]);

        // Demo researchers
        $main = User::create(['name' => 'Demo Researcher', 'email' => 'researcher@demo.bd', 'password' => 'password',
            'role' => 'researcher', 'phone' => '01822222222', 'balance' => 5000, 'reputation' => 120]);
        foreach ([['Nusrat Jahan', 340], ['Tanvir Hasan', 275], ['Sadia Rahman', 90]] as $i => [$n, $rep]) {
            User::create(['name' => $n, 'email' => "r$i@demo.bd", 'password' => 'password', 'role' => 'researcher',
                'reputation' => $rep]);
        }

        Report::create(['program_id' => $program->id, 'user_id' => $main->id, 'title' => 'Reflected XSS in search page',
            'type' => 'XSS', 'steps' => '1. Open /search?q=<script>alert(1)</script> 2. The script runs.',
            'cvss_vector' => 'CVSS:3.1/AV:N/AC:L/PR:N/UI:R/S:C/C:L/I:L/A:N', 'cvss_score' => 6.1, 'severity' => 'medium',
            'status' => 'resolved', 'reward_amount' => 8000, 'reward_paid' => true]);
    }
}
