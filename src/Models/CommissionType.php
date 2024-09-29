<?php

namespace Mkeremcansev\LaravelCommission\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class CommissionType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function models(): HasMany
    {
        return $this->hasMany(CommissionTypeModel::class);
    }

    public function hasModel($model): bool
    {
        return $this->models()->where('model_type', get_class($model))->exists();
    }

    public function getCommissionTypeModelsByModel(Model $model): Collection
    {
        if ($this->hasModel($model) === false) {
            return new Collection();
        }

        return $this->models()
            ->where('model_type', get_class($model))
            ->with('commissionType.commissions')
            ->get();
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class)->orderBy('order');
    }
}
