<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DueControl extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded = [];
    public function reseller()
    {
        return $this->belongsTo('App\Models\User','reseller_id','id');
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\User','reseller_parrent','id');
    }
}
