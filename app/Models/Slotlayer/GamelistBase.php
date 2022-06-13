<?php

namespace App\Models\Slotlayer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Slotlayer\Gameoptions;
use \Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GamelistBase extends Model
{

   use HasFactory;
   
   public $timestamps = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'gamelist';
     
    protected $fillable = [
        'id', 'game_id', 'game_slug', 'game_desc', 'game_provider', 'api_ext', 'hidden', 'type', 'disabled', 'extra_id'
    ];

    public static function cachedListings()
    {
        $gameslistCached = Cache::get('gameslistCached');  

        if (!$gameslistCached) { 
            $gameslistCached = self::all()->where('game_slug', '!=', NULL);

            /* Cache access profiles, time depending on the environment */
            if(env('APP_ENV') === 'staging') {
                $stagingCache = env('globalStagingCache');
                Cache::put('gameslistCached', $gameslistCached, Carbon::now()->addMinutes($stagingCache));
            } else {
                Cache::put('gameslistCached', $gameslistCached, Carbon::now()->addMinutes(10));
            }
        }

        return $gameslistCached;
    }


}