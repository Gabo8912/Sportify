<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        //admin
        $admin = User::firstOrCreate(
            ['email' => 'gavo00321@gmail.com'], 
            [
                'name' => 'Admin Gavo',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        Profile::updateOrCreate(['user_id' => $admin->id], [
            'location' => 'Berlin, Germany',
            'birth_date' => '2000-01-01',
            'position' => 'Administrator',
            'bio' => 'Head of Operations at Sportify.',
            'height' => 0, 'weight' => 0, 'dominant_foot' => 'Right', 'availability_status' => 'Available', 'current_club' => 'Staff'
        ]);

        //Players
        $players = [
            [
                'name' => 'Lionel Messi', 'email' => 'leo@miami.com', 'club' => 'Inter Miami', 
                'location' => 'Miami, USA', 'pos' => 'Forward', 'foot' => 'Left', 'height' => 170, 'weight' => 72
            ],
            [
                'name' => 'Cristiano Ronaldo', 'email' => 'cr7@nassr.com', 'club' => 'Al Nassr', 
                'location' => 'Riyadh, Saudi Arabia', 'pos' => 'Forward', 'foot' => 'Right', 'height' => 187, 'weight' => 83
            ],
            [
                'name' => 'Kylian Mbappé', 'email' => 'kylian@madrid.com', 'club' => 'Real Madrid', 
                'location' => 'Madrid, Spain', 'pos' => 'Forward', 'foot' => 'Right', 'height' => 178, 'weight' => 75
            ],
            [
                'name' => 'Erling Haaland', 'email' => 'robot@city.com', 'club' => 'Manchester City', 
                'location' => 'Manchester, UK', 'pos' => 'Forward', 'foot' => 'Left', 'height' => 195, 'weight' => 88
            ],
            [
                'name' => 'Jude Bellingham', 'email' => 'jude@madrid.com', 'club' => 'Real Madrid', 
                'location' => 'Madrid, Spain', 'pos' => 'Midfielder', 'foot' => 'Right', 'height' => 186, 'weight' => 75
            ],
            [
                'name' => 'Vinicius Jr', 'email' => 'vini@madrid.com', 'club' => 'Real Madrid', 
                'location' => 'Rio de Janeiro, Brazil', 'pos' => 'Forward', 'foot' => 'Right', 'height' => 176, 'weight' => 73
            ],
            [
                'name' => 'Kevin De Bruyne', 'email' => 'kdb@city.com', 'club' => 'Manchester City', 
                'location' => 'Ghent, Belgium', 'pos' => 'Midfielder', 'foot' => 'Right', 'height' => 181, 'weight' => 70
            ],
            [
                'name' => 'Virgil van Dijk', 'email' => 'vvd@liverpool.com', 'club' => 'Liverpool FC', 
                'location' => 'Breda, Netherlands', 'pos' => 'Defender', 'foot' => 'Right', 'height' => 193, 'weight' => 92
            ],
        ];

        foreach ($players as $p) {
            $user = User::create([
                'name' => $p['name'],
                'email' => $p['email'],
                'password' => Hash::make('password'),
                'role' => 'player',
                'email_verified_at' => now(),
            ]);

            Profile::create([
                'user_id' => $user->id,
                'current_club' => $p['club'],
                'location' => $p['location'],
                'position' => $p['pos'],
                'dominant_foot' => $p['foot'],
                'height' => $p['height'],
                'weight' => $p['weight'],
                'birth_date' => now()->subYears(rand(20, 35)),
                'availability_status' => 'Under Contract',
                'bio' => "Professional football player for {$p['club']}."
            ]);
        }

        //Scouts
        $scouts = [
            ['name' => 'Pep Guardiola', 'email' => 'pep@city.com', 'club' => 'City Group', 'location' => 'Manchester, UK'],
            ['name' => 'Jurgen Klopp', 'email' => 'klopp@redbull.com', 'club' => 'Red Bull Global', 'location' => 'Germany'],
            ['name' => 'Carlo Ancelotti', 'email' => 'carlo@madrid.com', 'club' => 'Real Madrid Scouting', 'location' => 'Italy'],
            ['name' => 'Zinedine Zidane', 'email' => 'zizou@france.com', 'club' => 'Independent Scout', 'location' => 'Marseille, France'],
        ];

        foreach ($scouts as $s) {
            $user = User::create([
                'name' => $s['name'],
                'email' => $s['email'],
                'password' => Hash::make('password'),
                'role' => 'scout', // Rol SCOUT
                'email_verified_at' => now(),
            ]);

            Profile::create([
                'user_id' => $user->id,
                'current_club' => $s['club'],
                'location' => $s['location'],
                'birth_date' => '1970-01-01',
                // Datos dummy obligatorios
                'position' => 'Head Scout', 'height' => 0, 'weight' => 0, 'dominant_foot' => 'Right', 'availability_status' => 'Available'
            ]);
        }
    }
}