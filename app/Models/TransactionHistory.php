<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionHistory extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function reseller()
    {
        return $this->belongsTo('App\Models\User','reseller_id','id');
    }
}
