<?php

namespace App\Console\Commands;
use App\Models\Slotlayer\GameoptionsParent;
use App\Models\Slotlayer\LogImportant;
use App\Models\Slotlayer\AccessProfiles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Slotlayer\GamelistBase;
use App\Models\Slotlayer\ListProvidersBase;
use App\Models\Slotlayer\BillingPerProvider;
use App\Models\Slotlayer\GametransactionsLive;
use Illuminate\Support\Str;

class ProcessGamesInternally extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slotlayer:processgametransactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Game Transactions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $gametransactions_live = GametransactionsLive::all();
        $countGamesToProcess = $gametransactions_live->count();
        if($countGamesToProcess < 1) {
            return;
        }
        foreach($gametransactions_live as $tx) {
            try {
            $selectOwner = $tx->ownedBy;
            $bet = (int) $tx->bet;
            $usd_exchange = $tx->usd_exchange;
            $game_id = $tx->gameid;
            $access_profile = $tx->access_profile;
            $win = (int) $tx->win;

            $getList = GamelistBase::cachedListings();
            $selectGameProvider = $getList->where('game_id', $game_id)->first();
            if(!$selectGameProvider) {
                $selectGameProvider = $getList->where('game_slug', $game_id)->first();
            }

            $getAccessProfile = AccessProfiles::getProviderPrice($access_profile, $selectGameProvider->game_provider);

            if(!$getAccessProfile) {
                LogImportant::insertImportantLog('2', 'Could not process transaction using access profile pricing, instead used base pricing for game: '.json_encode($tx));
                $pricing = ListProvidersBase::where('provider_id', $selectGameProvider->game_provider)->first()->ggr;
            } else {
                $pricing = floatval($getAccessProfile);
            } 

            $selectBillingStat = BillingPerProvider::where('provider_id', $selectGameProvider->game_provider)->where('ownedBy', $selectOwner)->first();
            if(!$selectBillingStat) {
                BillingPerProvider::insert([
                    'provider_id' => $selectGameProvider->game_provider,
                    'ownedBy' => $selectOwner,
                    'revenueBet' => 0,
                    'revenueWin' => 0,
                    'due' => 0,
                ]);
            $selectBillingStat = BillingPerProvider::where('provider_id', $selectGameProvider->game_provider)->where('ownedBy', $selectOwner)->first();
            }

            if($win > 0)
            {
                $revenueWin = floatval($win / 100);
                $winInUSDTotal = floatval($revenueWin / $usd_exchange);
                $winInUSDGGR = floatval(($winInUSDTotal / 100) * $pricing);
                //$winLog = 'Win in USD$ '.$winInUSDTotal.' - Win in GGR for '.$pricing.'%:  '.$winInUSDGGR.'$.';
            } else {
                $winInUSDTotal = 0;
                $winInUSDGGR = 0;
                $winLog = 'WIN 0';
            }

            if($bet > 0)
            {
                $revenueBet = floatval($bet / 100);
                $betInUSDTotal = floatval($revenueBet / $usd_exchange);
                $betInUSDGGR = floatval(($betInUSDTotal / 100) * $pricing);
                //$betLog = 'Bet in USD$ '.$betInUSDTotal.' - Bet in GGR for '.$pricing.'%:  '.$betInUSDGGR.'$.';
            } else {
                $betInUSDTotal = 0;
                $betInUSDGGR = 0;
                $betLog = 'BET 0';
            }

                $newcycle = floatval($selectBillingStat->due - $winInUSDGGR);
                $newcycle = floatval($newcycle + $betInUSDGGR);
                $newRevenueBet = floatval($selectBillingStat->revenueBet + $betInUSDTotal);
                $newRevenueWin = floatval($selectBillingStat->revenueWin + $winInUSDTotal);


                $selectBillingStat->update(['due' => $newcycle, 'revenueWin' => $newRevenueWin, 'revenueBet' => $newRevenueBet]);

            try {

                $newPost = $tx->replicate();
                $newPost->id = Str::uuid();
                $newPost->created_at = $tx->created_at_orig;
                $newPost->setTable('gametransactions');
                $newPost->save();
                $tx->delete();

            } catch(Exception $e) {
                    LogImportant::insertImportantLog('5', 'Error trying to move record to archive database table');
            }

                //LogImportant::insertImportantLog('5', 'BET '.$bet.' WIN'.$win.' '.$tx->gameid.' - Round: '.$tx->roundid.' BET LOG = '.$betLog.' - WIN LOG = '.$winLog);

            /*
            $getAccessProfile = AccessProfiles::getProviders($access_profile, $selectGameProvider->game_provider);

            if(!$getAccessProfile || $getAccessProfile === false) {
                LogImportant::insertImportantLog('2', 'Could not process transaction using access profile pricing, instead used base pricing for game: '.json_encode($tx));
                $pricing = ListProvidersBase::where($selectGameProvider->game_provider->first();
            } else {
                $pricing = $getAccessProfile;
            }
            */




            } catch(Exception $e) {
                LogImportant::insertImportantLog('5', 'Error processing game: '.json_encode($tx));
        }

        }
                    LogImportant::insertImportantLog('0', 'Processed '.$countGamesToProcess.' games.');


    }
}