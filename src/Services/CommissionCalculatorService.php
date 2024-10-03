<?php

declare(strict_types=1);

namespace Mkeremcansev\LaravelCommission\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Mkeremcansev\LaravelCommission\Contracts\HasCommissionInterface;
use Mkeremcansev\LaravelCommission\Models\Commission;
use Mkeremcansev\LaravelCommission\Models\CommissionType;
use Mkeremcansev\LaravelCommission\Services\Contexts\CommissionBundleContext;

class CommissionCalculatorService
{
    public array $columns = [];

    /**
     * @throws Exception
     */
    public function __construct(public Model&HasCommissionInterface $model)
    {
        $this->columns = $this->model->getCommissionableColumns();

        $this->validateColumnsExistence($this->columns);
    }

    /**
     * @throws Exception
     */
    public function getCalculableCommissions(): array
    {
        $commissions = [];
        $commissionGroupId = Str::uuid()->toString();

        foreach ($this->columns as $column) {
            $columnCommissions = $this->getCommissionsWithColumn($column, $commissionGroupId);

            $commissions = array_merge($commissions, $columnCommissions);
        }

        return $commissions;
    }

    public function getCommissionsWithColumn(string $column, string $commissionGroupId): array
    {
        $commissionTypes = CommissionType::query()
            ->withWhereHas('commissions', function ($query) {
                $query->active();
            })
            ->withWhereHas('commissionTypeModels', function ($query) {
                $query->where('model_type', get_class($this->model))
                    ->where(function ($query) {
                        $query->whereNull('model_id')
                            ->orWhere('model_id', $this->model->id);
                    });
            })
            ->get();

        $commissions = [];
        foreach ($commissionTypes as $commissionType) {

            $commissionWithColumn = $commissionType->commissions->map(function (Commission $commission) use ($column, $commissionGroupId) {
                return new CommissionBundleContext(
                    commission: $commission,
                    column: $column,
                    commissionGroupId: $commissionGroupId
                );
            })->toArray();

            $commissions = array_merge($commissions, $commissionWithColumn);
        }

        return $commissions;
    }

    /**
     * @throws Exception
     */
    public function validateColumnsExistence(array $columns): void
    {
        foreach ($columns as $column) {
            if (Schema::hasColumn($this->model->getTable(), $column) === false) {
                throw new Exception("Column {$column} does not exist in table {$this->model->getTable()}");
            }

            if (is_numeric($this->model->{$column}) === false) {
                throw new Exception("Column {$column} is not numeric");
            }
        }
    }
}
