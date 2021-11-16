<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResellerProfit extends Model
{
    //use HasFactory;
    protected $guarded = [];
    public function reseller_profit()
    {
        return $this->belongsTo('App\Models\user','reseller_id','id');
    }

}
