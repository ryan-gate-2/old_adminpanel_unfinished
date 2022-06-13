<?php

namespace App\Models\Slotlayer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Slotlayer\Gameoptions;
use \Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AccessGamesList extends Model
{

   use HasFactory;
   public $timestamps = true;
    public $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'access_games';
     
    protected $fillable = [
        'id', 'game', 'name', 'demo', 'type', 'descr', 'current_index_rating', 'hidden', 'active', 'rtp', 'bonusbuy', 'provider', 'price', 'access_profile', 'base_index'
    ];


    public function accessprofile()
    {
        return $this->belongsTo('App\Models\Slotlayer\AccessProfiles', 'access_profile', 'id');
    }
    public function accessproviders()
    {
        return $this->belongsTo('App\Models\Slotlayer\AccessProfiles', 'access_profile', 'id');
    }


}