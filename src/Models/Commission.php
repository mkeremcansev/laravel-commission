<?php

namespace Mkeremcansev\LaravelCommission\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mkeremcansev\LaravelCommission\Enums\CommissionTypeEnum;

class Commission extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'commission_type_id',
        'rate',
        'amount',
        'min_amount',
        'max_amount',
        'type',
        'start_date',
        'end_date',
        'status',
        'is_total',
        'order',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => CommissionTypeEnum::class,
            'status' => 'boolean',
            'is_total' => 'boolean',
            'rate' => 'float',
        ];
    }

    public function histories(): HasMany
    {
        return $this->hasMany(CommissionCalculateHistory::class);
    }
}
