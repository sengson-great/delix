<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Model;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ThirdParty extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'provider',
        'description',
        'api_key',
        'api_secret',
        'api_url',
        'api_version',
        'username',
        'password',
        'merchant_id',
        'store_id',
        'signature_key',
        'address',
        'phone_number',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'status' => StatusEnum::class,
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $user = Sentinel::check();
            $model->created_by = $user ? $user->id : null;
            $model->status = $model->status ?? StatusEnum::ACTIVE; // Default status
        });

        static::updating(function ($model) {
            $user = Sentinel::check();
            $model->updated_by = $user ? $user->id : null;
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', StatusEnum::ACTIVE);
    }
}