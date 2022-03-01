<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ticket extends Model
{
    protected $guarded = [];
    //use HasFactory;
    public function reseller()
    {
        return $this->belongsTo('App\Models\User','reseller_id','id');
    }
    public function last_response()
    {
        return $this->hasOne('App\Models\ticket_response','ticket_id','id')->latestOfMany();
    }
}
