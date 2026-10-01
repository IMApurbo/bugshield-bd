<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $guarded = [];
    protected $casts = ['approved_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function programs() { return $this->hasMany(Program::class); }
}
