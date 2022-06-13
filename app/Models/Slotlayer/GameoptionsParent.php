<?php

namespace App\Models\Slotlayer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Slotlayer\AccessProfiles;

class GameoptionsParent extends Model
{

   use HasFactory;

   public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'gameoptions_parent';
     
    protected $fillable = [
        'id', 'apikey_parent', 'hourly_spare_demosessions', 'hourly_spare_createsession_errors', 'hourly_spare_callback_errors', 'ownedBy', 'operatorurl', 'operator_secret', '
        ', 'real_sessions_stat', 'poker_prefix', 'slots_prefix', 'access_profile', 'callbackurl', 'extendedApi', 'allowed_ips', 'native_currency', 'poker_enabled', 'updated_at', 'created_at', 'active', 'return_log'
    ];
    
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'ownedBy');
    }
    public function accessprofile()
    {
        return $this->belongsTo('App\Models\Slotlayer\AccessProfiles', 'access_profile', 'id');
    }
    public function gameoptions()
    {
        return $this->hasMany('App\Models\Slotlayer\Gameoptions', 'apikey_parent', 'parent_key');
    }

}