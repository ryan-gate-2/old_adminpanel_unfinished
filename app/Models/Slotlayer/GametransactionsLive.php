<?php

namespace App\Models\Slotlayer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

class GametransactionsLive extends Model
{
   use HasFactory;
   
   public $timestamps = true;
   public $primaryKey = 'id';
   public $uuidKey = 'id';
   public $incrementing = false; 

    protected $table = 'gametransactions_live';
  
    protected $fillable = [
        'casinoid', 'player', 'ownedBy', 'bet', 'win', 'usd_exchange', 'currency', 'access_profile', 'gameid', 'txid', 'final', 'type', 'callback_state', 'rawdata', 'roundid',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
    
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'ownedBy');
    }

}