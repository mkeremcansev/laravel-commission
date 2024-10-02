<?php

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

        foreach ($this->columns as $column) {
            $this->setDefaultAttributes($column);

            $columnCommissions = $this->getCommissionsWithColumn($column);

            $commissions = array_merge($commissions, $columnCommissions);
        }

        return $commissions;
    }

    public function getCommissionsWithColumn(string $column): array
    {
        $commissionTypes = CommissionType::query()
            ->with([
                'commissionTypeModels',
                'commissions',
            ])
            ->whereHas('commissionTypeModels', function ($query) {
                $query->where('model_type', get_class($this->model))
                    ->where(function ($query) {
                        $query->whereNull('model_id')
                            ->orWhere('model_id', $this->model->id);
                    });
            })
            ->whereHas('commissions', function ($query) {
                $query->active();
            })
            ->get();

        $commissions = [];
        foreach ($commissionTypes as $commissionType) {

            $commissionWithColumn = $commissionType->commissions->map(function (Commission $commission) use ($column) {
                return new CommissionBundleContext(
                    commission: $commission,
                    column: $column
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

    public function setDefaultAttributes(string $column): void
    {
        $this->model->current_calculation_column = $column;

        if ($this->model->commission_group_id === null) {
            $this->model->commission_group_id = Str::uuid()->toString();
        }
    }
}
