<?php

namespace App\Models;

use App\Models\Account\Account;
use App\Models\Account\MerchantWithdraw;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class WithdrawBatch extends Model
{
    use HasFactory;

    protected $table = 'withdraw_batches';
    
    protected $fillable = [
        'title',
        'batch_number',
        'type',
        'notes',
        'user_id',
        'created_by',
        'updated_by',
        'status',
        'batch_date',
        'total_requests',
        'total_processed',
        'total_pending',
        'total_rejected',
        'total_amount',
        'total_charge',
        'total_payable',
        'total_processed_amount',
        'total_pending_amount',
        'total_rejected_amount',
        'payment_method',
        'file_path',
        'summary',
        'processed_at',
        'completed_at',
        'processed_by',
        'account_id'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function withdraws()
    {
        return $this->hasMany(MerchantWithdraw::class, 'withdraw_batch_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $user = Sentinel::check();
            $model->created_by = $user ? $user->id : null;
            $model->created_at = now(); // Use Laravel helper instead of date()
        });

        static::updating(function ($model) {
            $user = Sentinel::check();
            $model->updated_by = $user ? $user->id : null;
            $model->updated_at = now(); // Use Laravel helper instead of date()
        });
    }
}