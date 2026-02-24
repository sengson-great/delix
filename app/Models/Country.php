<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'iso3', 'iso2', 'phonecode', 'currency', 'currency_symbol', 'latitude', 'longitude', 'status'];
    protected $casts = [
        'status' => StatusEnum::class,
    ];

    public function flag()
    {
        return $this->hasOne(FlagIcon::class, 'country_code', 'iso2');
    }

    public function getFlagIconAttribute()
    {
        // Try to get the flag model
        $flag = null;
        
        if ($this->flag instanceof \App\Models\FlagIcon) {
            $flag = $this->flag;
        } elseif (is_numeric($this->flag)) {
            $flag = \App\Models\FlagIcon::find($this->flag);
        }
        
        if ($flag && $flag->image) {
            return static_asset($flag->image);
        }
        
        return static_asset('images/default/default-image-40x40.png');
    }

    public function scopeActive($query)
    {
        return $query->where('status', StatusEnum::ACTIVE);
    }
}
