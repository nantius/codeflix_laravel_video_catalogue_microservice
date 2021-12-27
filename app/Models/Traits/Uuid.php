<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait Uuid
{
    public static function boot()
    {
        parent::boot();
        static::creating(function ($obj) {
            $obj->id = Str::uuid();
        });
    }
}
