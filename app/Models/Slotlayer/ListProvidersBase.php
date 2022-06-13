<?php

namespace App\Models\Slotlayer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Slotlayer\Gameoptions;
use \Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Webpatser\Uuid\Uuid;

class ListProvidersBase extends Model
{

   use HasFactory;
   
   public $timestamps = true;
   public $primaryKey = 'id';
   public $uuidKey = 'id';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'list_providers_base';
     
    protected $fillable = [
        'id', 'provider_id', 'provider_name', 'ggr', 'index_rating', 'ggr_cost', 'softswiss_id', 'active'
    ];
    
    public static function countTotalProviders()
    {
        $countTotalProviders = Cache::get('countTotalProviders');  

        if (!$countTotalProviders) { 
            $countTotalProviders = self::count();
            if(env('APP_ENV') === 'staging') {
                $stagingCache = env('globalStagingCache');
                Cache::put('countTotalProviders', $countTotalProviders, Carbon::now()->addMinutes($stagingCache));
            } else {
                Cache::put('countTotalProviders', $countTotalProviders, Carbon::now()->addMinutes(5));
            }
        }

        return $countTotalProviders;
    }


    public static function basePrice($provider)
    {
        $getProviderBase = Cache::get('basePrice:'.$provider);  

        if (!$getProviderBase) { 
            $selectProvider = self::where('provider_id', $provider)->first();
            if(!$selectProvider) {
                return 10;
            }
            $getProviderBase = $selectProvider->ggr;
            if(env('APP_ENV') === 'staging') {
                $stagingCache = env('globalStagingCache');
                Cache::put('basePrice:'.$provider, $getProviderBase, Carbon::now()->addMinutes($stagingCache));
            } else {
                Cache::put('basePrice:'.$provider, $getProviderBase, Carbon::now()->addMinutes(15));
            }
        }

        if($getProviderBase < 0.1) {
            $getProviderBase = 0.5;
        }

        return $getProviderBase;
    }

    public static function costPrice($provider)
    {
        $getProviderCost = Cache::get('providerCostPrice:'.$provider);  

        if (!$getProviderCost) { 
            $selectProvider = self::where('provider_id', $provider)->first();
            if(!$selectProvider) {
                return 10;
            }
            $getProviderCost = $selectProvider->ggr_cost;
            if(env('APP_ENV') === 'staging') {
                $stagingCache = env('globalStagingCache');
                Cache::put('providerCostPrice:'.$provider, $getProviderCost, Carbon::now()->addMinutes($stagingCache));
            } else {
                Cache::put('providerCostPrice:'.$provider, $getProviderCost, Carbon::now()->addMinutes(15));
            }
        }

        if($getProviderCost < 0.1) {
            $getProviderCost = 0.5;
        }

        return $getProviderCost;
    }


}