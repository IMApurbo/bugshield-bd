<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $guarded = [];
    protected $casts = ['active' => 'boolean'];

    public function organization() { return $this->belongsTo(Organization::class); }
    public function reports() { return $this->hasMany(Report::class); }

    public function rewardFor(?string $severity): int
    {
        return match ($severity) {
            'critical' => $this->reward_critical,
            'high' => $this->reward_high,
            'medium' => $this->reward_medium,
            'low' => $this->reward_low,
            default => 0,
        };
    }
}
