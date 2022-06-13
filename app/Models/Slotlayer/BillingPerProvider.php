<?php

namespace App\Models\Slotlayer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Slotlayer\Gameoptions;
use \Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BillingPerProvider extends Model
{

   use HasFactory;
   public $timestamps = true;
    public $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'billing_providers';
     
    protected $fillable = [
        'id', 'provider_id', 'ownedBy', 'revenueWin', 'revenueBet', 'due'
    ];


}