<?php

namespace Mkeremcansev\LaravelCommission\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionTypeModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'commission_type_id',
        'model_id',
        'model_type',
    ];

    public function commissionType(): BelongsTo
    {
        return $this->belongsTo(CommissionType::class);
    }
}
