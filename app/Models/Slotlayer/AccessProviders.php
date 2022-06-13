<?php

namespace App\Models\Slotlayer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Slotlayer\Gameoptions;
use \Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AccessProviders extends Model
{

   use HasFactory;
   public $timestamps = true;
    public $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'access_providers';
     
    protected $fillable = [
        'id', 'provider', 'price', 'access_profile', 'base_index'
    ];


    public function accessprofile()
    {
        return $this->belongsTo('App\Models\Slotlayer\AccessProfiles', 'access_profile', 'id');
    }
    public function accessproviders()
    {
        return $this->belongsTo('App\Models\Slotlayer\AccessProfiles', 'access_profile', 'id');
    }

    public static function countLoadedProviders($profile_id)
    {
        $getLoadedProviders = Cache::get('countLoadedProviders:'.$profile_id);  

        if (!$getLoadedProviders) { 
            $countProvider = self::where('access_profile', $profile_id)->count();

            if($countProvider < 1) {
                return 0;
            }
            $getLoadedProviders = $countProvider;
            if(env('APP_ENV') === 'staging') {
                $stagingCache = env('globalStagingCache');
                Cache::put('countLoadedProviders:'.$profile_id, $getLoadedProviders, Carbon::now()->addMinutes($stagingCache));
            } else {
                Cache::put('countLoadedProviders:'.$profile_id, $getLoadedProviders, Carbon::now()->addMinutes(5));
            }
        }

        return $getLoadedProviders;
    }

}