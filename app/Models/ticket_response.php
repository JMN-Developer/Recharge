<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ticket_response extends Model
{
    protected $guarded = [];
    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id','id');
    }
    use HasFactory;
}
