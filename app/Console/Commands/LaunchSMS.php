<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

class LaunchSMS extends Command
{
use Notifiable;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tollgate:sendsms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


      $profile = '1';

    foreach(\App\Models\Slotlayer\ListProvidersBase::all() as $provider) {

        $searchIfExistsInModel = \App\Models\Slotlayer\AccessProviders::where('access_profile', '=', $profile)->where('provider', '=', $provider->provider_id)->first();

        if(!$searchIfExistsInModel) {
            $pricing = $provider->ggr;

            \App\Models\Slotlayer\AccessProviders::insert([
                'provider' => $provider->provider_id,
                'price' => $pricing,
                'access_profile' => $profile,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

      }




    }
}
