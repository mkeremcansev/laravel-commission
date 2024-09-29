<?php

namespace Mkeremcansev\LaravelCommission\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryReasonEnum;
use Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryStatusEnum;

class CommissionCalculateHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'commission_id',
        'model_id',
        'model_type',
        'group_id',
        'column',
        'original_amount',
        'calculated_amount',
        'commission_amount',
        'status',
        'reason',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => CommissionCalculateHistoryStatusEnum::class,
            'reason' => CommissionCalculateHistoryReasonEnum::class,
        ];
    }
}
