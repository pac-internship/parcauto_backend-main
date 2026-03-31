<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JournalSms;
use App\Models\User;
use Carbon\Carbon;

class JournalSmsSeeder extends Seeder
{
    public function run(): void
    {
        if (User::count() == 0) {
            $this->command->warn('Aucun utilisateur trouvé. Seeder annulé.');
            return;
        }

        $users = User::pluck('id');

        for ($i = 1; $i <= 10; $i++) {

            JournalSms::create([
                'contact' => '97' . rand(100000, 999999),
                'contenu' => 'Votre demande REF-' . rand(100,999) . ' a été traitée.',
                'status_envoi' => rand(0,1) ? 'CREE' : 'ENVOYE',
                'date_envoi' => Carbon::now()->subMinutes(rand(1, 500)),
                'user_id' => $users->random(),
            ]);
        }
    }
}