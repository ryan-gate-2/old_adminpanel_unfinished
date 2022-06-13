<?php

namespace App\Models\Slotlayer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Slotlayer\GameoptionsParent;
use App\Models\Slotlayer\Gameoptions;
use Webpatser\Uuid\Uuid;
use Illuminate\Support\Str;

class LogImportant extends Model
{

   use HasFactory;

   public $timestamps = true;
   public $primaryKey = 'uid';
   public $uuidKey = 'uid';
   public $incrementing = false; 

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'log_important';
     
    protected $fillable = [
        'log_level', 'log_message', 'notified'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
 

    public static function insertImportantLog($log_level, $log_message) {


            $sessionLog = self::insert([
                'uid' =>  Str::uuid(),
                'log_level' => $log_level,
                'log_message' => $log_message,
                'notified' => 0,
                'updated_at' => now(),
                'created_at' => now()
            ]);

        return true;
    }

}