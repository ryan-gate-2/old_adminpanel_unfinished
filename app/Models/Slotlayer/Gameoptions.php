<?php

namespace App\Models\Slotlayer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Slotlayer\GameoptionsParent;

class Gameoptions extends Model
{
 
   use HasFactory;

   public $timestamps = true;
   public $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'gameoptions';
     
    protected $fillable = [
        'id', 'apikey', 'ownedBy', 'operatorurl', 'parent_key', 'operator_secret', '
        ', 'access_profile', 'demo_sessions', 'real_sessions', 'native_currency', 'poker_enabled', 'updated_at', 'created_at', 'active', 'return_log'
    ];

 
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];


    public function user()
    {
        return $this->belongsTo('App\Models\User', 'ownedBy');
    }
    public function parentkey()
    {
        return $this->belongsTo('App\Models\Slotlayer\GameoptionsParent', 'parent_key', 'apikey_parent');
    }

}