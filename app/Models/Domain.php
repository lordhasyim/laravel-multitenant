<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Models\Domain as BaseDomain;


class Domain extends BaseDomain
{
    protected $fillable = [
        'domain',
        'tenant_id',
    ];
}
