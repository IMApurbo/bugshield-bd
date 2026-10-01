<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $guarded = [];
    protected $casts = ['reward_paid' => 'boolean'];

    public function program() { return $this->belongsTo(Program::class); }
    public function researcher() { return $this->belongsTo(User::class, 'user_id'); }
    public function comments() { return $this->hasMany(Comment::class)->oldest(); }
}
