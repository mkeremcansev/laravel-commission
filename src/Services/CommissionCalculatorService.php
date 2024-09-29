<?php

namespace Mkeremcansev\LaravelCommission\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Mkeremcansev\LaravelCommission\Models\CommissionType;
use Mkeremcansev\LaravelCommission\Models\CommissionTypeModel;

class CommissionCalculatorService
{
    public function __construct(public Model $model) {}

    /**
     * @throws Exception
     */
    public function getCalculableCommissions(array $columns): array
    {
        $columns = collect($columns);

        $this->validateColumnsExistence(columns: $columns);

        return $columns
            ->flatMap(function ($column) {
                $this->setDefaultAttributes(column: $column);

                return $this->getCommissionsForColumn(column: $column);
            })
            ->filter()
            ->all();
    }

    /**
     * @throws Exception
     */
    public function validateColumnsExistence(Collection $columns): void
    {
        foreach ($columns as $column) {
            if (! Schema::hasColumn($this->model->getTable(), $column)) {
                throw new Exception("Column {$column} does not exist in table {$this->model->getTable()}");
            }
        }
    }

    public function setDefaultAttributes(string $column)
    {
        $this->model->current_calculation_column = $column;
        $this->model->group_id = Str::uuid()->toString();
    }

    public function getCommissionsForColumn(string $column)
    {
        return CommissionType::all()
            ->filter(fn ($commissionType) => $commissionType->hasModel(model: $this->model))
            ->flatMap(fn ($commissionType) => $this->getCommissionsFromType(commissionType: $commissionType, column: $column));
    }

    public function getCommissionsFromType(CommissionType $commissionType, string $column)
    {
        return $commissionType->getCommissionTypeModelsByModel(model: $this->model)
            ->filter(fn ($commissionTypeModel) => $this->isValidCommissionModel(commissionTypeModel: $commissionTypeModel))
            ->flatMap(fn ($commissionTypeModel) => $commissionTypeModel->commissionType->commissions->map(fn ($commission) => [
                'commission' => $commission,
                'column' => $column,
            ]));
    }

    public function isValidCommissionModel(CommissionTypeModel $commissionTypeModel): bool
    {
        return is_null($commissionTypeModel->model_id) || $commissionTypeModel->model_id === $this->model->id;
    }
}
