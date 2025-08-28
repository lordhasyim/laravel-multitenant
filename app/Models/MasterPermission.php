<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterPermission extends Model
{
    protected $connection = 'central';
    
    protected $fillable = [
        'name',
        'guard_name', 
        'category',
        'description'
    ];
}
