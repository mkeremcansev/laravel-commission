<?php

declare(strict_types=1);

namespace Mkeremcansev\LaravelCommission\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommissionType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function commissionTypeModels(): HasMany
    {
        return $this->hasMany(CommissionTypeModel::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class)->orderBy('order');
    }
}
