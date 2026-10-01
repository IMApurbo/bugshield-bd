<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $guarded = [];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed', 'balance' => 'decimal:2'];
    }

    public function organization() { return $this->hasOne(Organization::class); }
    public function reports() { return $this->hasMany(Report::class); }
    public function withdrawals() { return $this->hasMany(Withdrawal::class)->latest(); }
    public function isAdmin(): bool { return $this->role === 'admin'; }
}
