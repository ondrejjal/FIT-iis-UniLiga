<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /* ----- USERS ------ */
        // Admin user
        $admin = \App\Models\User::create([
            'username' => 'admin',
            'first_name' => 'Admin',
            'surname' => 'User',
            'email' => 'admin@example.com',
            'password_hash' => bcrypt('password'),
            'phone_number' => null,
            'role' => 'admin',
        ]);

        // Regular test user
        $testUser = \App\Models\User::create([
            'username' => 'testuser',
            'first_name' => 'Test',
            'surname' => 'User',
            'email' => 'test@example.com',
            'password_hash' => bcrypt('password'),
            'phone_number' => null,
            'role' => 'user',
        ]);

        /* ----- TEAMS ------ */
        $num_users = 15;
        $users = collect();
        $teams = collect();

        for ($i = 0; $i < $num_users; $i++) {
            $usr = \App\Models\User::create([
                'username' => 'testuser' . $i,
                'first_name' => 'Test',
                'surname' => 'User' . $i,
                'email' => 'test' . $i . '@example.com',
                'password_hash' => bcrypt('password'),
                'phone_number' => null,
                'role' => 'user',
            ]);

            if ($i % 5 == 0) {
                $team = \App\Models\Team::create([
                    'user_id' => $usr->id,
                    'name' => "Test Team " . ($i / 5 + 1),
                ]);
                $teams->push($team);

                // Add the captain to their own team
                $team->players()->attach($usr->id);
            } else {
                $users->push($usr);

                // Add users to teams (assign to the last created team)
                if ($teams->isNotEmpty()) {
                    $teams->last()->players()->attach($usr->id);
                }
            }
        }

        /* ----- INDIVIDUAL TOURNAMENTS ------ */

        // 1. Approved individual tournament with approved participants
        $individualTournament1 = \App\Models\Tournament::create([
            'user_id' => $admin->id,
            'pending' => false,
            'type' => 'individual',
            'max_participants' => 16,
            'min_participants' => 4,
            'name' => 'Spring Championship 2025',
            'date' => '2025-12-15',
            'starting_time' => '14:00:00',
            'description' => 'Annual spring tournament for all skill levels. Sign up now!',
        ]);

        // Add approved participants
        $individualTournament1->singleContestants()->attach($testUser->id, ['pending' => false, 'order' => 1]);
        foreach ($users->take(5) as $index => $user) {
            $individualTournament1->singleContestants()->attach($user->id, ['pending' => false, 'order' => $index + 2]);
        }

        // 2. Pending individual tournament
        $individualTournament2 = \App\Models\Tournament::create([
            'user_id' => $testUser->id,
            'pending' => true,
            'type' => 'individual',
            'max_participants' => 8,
            'min_participants' => 4,
            'name' => 'Quick Match Tournament',
            'date' => '2025-11-25',
            'starting_time' => '18:00:00',
            'description' => 'Fast-paced evening tournament. Awaiting admin approval.',
        ]);

        // 3. Individual tournament with mix of pending and approved participants
        $individualTournament3 = \App\Models\Tournament::create([
            'user_id' => $users->first()->id,
            'pending' => false,
            'type' => 'individual',
            'max_participants' => 32,
            'min_participants' => 8,
            'name' => 'Winter League Finals',
            'date' => '2025-12-20',
            'starting_time' => '10:00:00',
            'description' => 'The grand finale of the winter season!',
        ]);

        // Add mix of approved and pending participants
        $individualTournament3->singleContestants()->attach($admin->id, ['pending' => false, 'order' => 1]);
        $individualTournament3->singleContestants()->attach($testUser->id, ['pending' => true, 'order' => 2]);
        foreach ($users->slice(5, 6) as $index => $user) {
            $individualTournament3->singleContestants()->attach($user->id, [
                'pending' => $index % 2 == 0,
                'order' => $index + 3
            ]);
        }

        /* ----- TEAM TOURNAMENTS ------ */

        // 1. Approved team tournament with approved team participants
        $teamTournament1 = \App\Models\Tournament::create([
            'user_id' => $admin->id,
            'pending' => false,
            'type' => 'team',
            'max_participants' => 8,
            'min_participants' => 4,
            'name' => 'Team Clash 2025',
            'date' => '2025-12-10',
            'starting_time' => '15:00:00',
            'description' => 'Battle it out with your team! 5v5 format.',
        ]);

        // Add approved team participants
        foreach ($teams->take(4) as $index => $team) {
            $teamTournament1->teamContestants()->attach($team->id, ['pending' => false, 'order' => $index + 1]);
        }

        // 2. Pending team tournament
        $teamTournament2 = \App\Models\Tournament::create([
            'user_id' => $teams->first()->user_id,
            'pending' => true,
            'type' => 'team',
            'max_participants' => 16,
            'min_participants' => 4,
            'name' => 'UniLiga Champions Cup',
            'date' => '2026-01-15',
            'starting_time' => '12:00:00',
            'description' => 'Championship tournament for university teams. Pending approval.',
        ]);

        // Add some pending teams to this tournament
        foreach ($teams->take(2) as $index => $team) {
            $teamTournament2->teamContestants()->attach($team->id, ['pending' => true, 'order' => $index + 1]);
        }

        // 3. Team tournament with mix of pending and approved participants
        $teamTournament3 = \App\Models\Tournament::create([
            'user_id' => $testUser->id,
            'pending' => false,
            'type' => 'team',
            'max_participants' => 8,
            'min_participants' => 2,
            'name' => 'Weekend Warriors Tournament',
            'date' => '2025-11-30',
            'starting_time' => '09:00:00',
            'description' => 'Casual weekend team tournament. All welcome!',
        ]);

        // Add mix of approved and pending team participants
        foreach ($teams as $index => $team) {
            if ($index < 3) {
                $teamTournament3->teamContestants()->attach($team->id, [
                    'pending' => $index == 1, // Second team is pending
                    'order' => $index + 1
                ]);
            }
        }

        // 4. Nearly full individual tournament
        $individualTournament4 = \App\Models\Tournament::create([
            'user_id' => $admin->id,
            'pending' => false,
            'type' => 'individual',
            'max_participants' => 8,
            'min_participants' => 4,
            'name' => 'Elite Masters Tournament',
            'date' => '2026-01-05',
            'starting_time' => '16:00:00',
            'description' => 'High-level competition. Limited spots available!',
        ]);

        // Fill it almost to capacity (7 out of 8)
        $individualTournament4->singleContestants()->attach($admin->id, ['pending' => false, 'order' => 1]);
        foreach ($users->take(6) as $index => $user) {
            $individualTournament4->singleContestants()->attach($user->id, ['pending' => false, 'order' => $index + 2]);
        }
    }
}