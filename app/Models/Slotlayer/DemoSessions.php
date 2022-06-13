<?php

namespace App\Models\Slotlayer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Slotlayer\Gameoptions;
use \Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Webpatser\Uuid\Uuid;

class DemoSessions
{

   use HasFactory;
   
   public $timestamps = true;
   public $primaryKey = 'uid';
   public $uuidKey = 'uid';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'demo_sessions';
     
    protected $fillable = [
        'uid', 'casino_id', 'session_id', 'currency', 'player_id', 'player_meta', 'player_ip', 'game', 'request_ip', 'visited', 'active'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

}