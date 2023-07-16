<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class bus extends Model
{
    //use HasFactory;
    protected $guarded = [];
    public function resller()
    {
        return $this->belongsTo(User::class, 'reseller_id');
    }
}
