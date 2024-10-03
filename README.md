
<picture>
  <source media="(prefers-color-scheme: dark)" srcset="https://banners.beyondco.de/Laravel%20Commission.png?theme=dark&packageManager=composer+require&packageName=mkeremcansev%2Flaravel-commission&pattern=architect&style=style_1&description=A+flexible+package+to+calculate+and+log+commissions+in+Laravel.&md=1&showWatermark=1&fontSize=100px&images=receipt-tax">
  <source media="(prefers-color-scheme: light)" srcset="https://banners.beyondco.de/Laravel%20Commission.png?theme=light&packageManager=composer+require&packageName=mkeremcansev%2Flaravel-commission&pattern=architect&style=style_1&description=A+flexible+package+to+calculate+and+log+commissions+in+Laravel.&md=1&showWatermark=1&fontSize=100px&images=receipt-tax">
  <img alt="Package Image" src="https://banners.beyondco.de/Laravel%20Commission.png?theme=light&packageManager=composer+require&packageName=mkeremcansev%2Flaravel-commission&pattern=architect&style=style_1&description=A+flexible+package+to+calculate+and+log+commissions+in+Laravel.&md=1&showWatermark=1&fontSize=100px&images=receipt-tax">
</picture>

## Installation


You can install the package via composer:

```bash
composer require mkeremcansev/laravel-commission
```

You can publish the migrations with:

```bash
php artisan vendor:publish --tag="laravel-commission-migrations"
```

You can run the migrations with:
```bash
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-commission-config"
```

## Models

### 1. CommissionType
This model represents the types of commissions that can be created. It contains information about the commission's name and optional description.

**Attributes:**
- `id`: The unique identifier for the commission type.
- `name`: The name of the commission type.
- `description`: An optional description of the commission type.
- `created_at`, `updated_at`: Timestamps for when the commission type was created and last updated.

### 2. Commission
This model stores individual commission records associated with a `CommissionType`. It includes details about the commission's rate, applicable amount, and date range.

**Attributes:**
- `id`: The unique identifier for the commission.
- `commission_type_id`: Foreign key referencing the `CommissionType`.
- `rate`: The percentage rate of the commission (e.g., 10.00%). This field is determined based on the `type` column.
- `amount`: The specific amount associated with this commission (e.g., 1000 = $10.00). This field is determined based on the `type` column.
- `min_amount`: The minimum amount threshold for applying this commission. **(Optional)**
- `max_amount`: The maximum amount threshold for applying this commission. **(Optional)**
- `type`: The type of commission, which uses `\Mkeremcansev\LaravelCommission\Enums\CommissionTypeEnum` to define the type (e.g., percentage-based or fixed).
- `start_date`: The start date for when this commission becomes active. **(Optional)**
- `end_date`: The end date for when this commission is no longer active. **(Optional)**
- `status`: A boolean value indicating whether the commission is currently active.
- `is_total`: A boolean indicating how this commission should be applied. If `true`, the commission is applied on the total amount after previous commissions have been calculated. If `false`, the commission is calculated based only on its own defined amount or rate.
- `rounding`: The rounding logic used for the commission calculation, which uses the `\Mkeremcansev\LaravelCommission\Enums\CommissionRoundingEnum` to define the rounding strategy.
- `order`: Defines the order in which commissions should be applied (useful for calculating multiple commissions).
- `created_at`, `updated_at`: Timestamps for when the commission was created and last updated.



### 3. CommissionTypeModel
This model links a `CommissionType` to either a specific model or globally to all models of a particular type.

**Attributes:**
- `commission_type_id`: Foreign key referencing the `CommissionType`.
- `model_id`: The ID of the specific model to which the commission type applies. If `null`, the commission type applies to all models.
- `model_type`: The type of the model (e.g., `Product`, `Order`).


### 4. CommissionCalculateHistory
This model logs each commission calculation, storing details about the original amount, the calculated amount, and the commission applied. It also keeps track of which model the commission was applied to and groups related calculations.

###  Important Note

The data in this table can be deleted after each calculation. The package does not delete it by default, but you can do so. **Recommendation:** Clear the table each time the system is put into maintenance mode. This is advised because there may be ongoing calculations that rely on data from this table, which could lead to incorrect computations.

**Attributes:**
- `id`: The unique identifier for the history record.
- `commission_id`: Foreign key referencing the `Commission`.
- `model_id`: The ID of the model the commission was applied to.
- `model_type`: The type of the model (e.g., `Product`, `Order`).
- `group_id`: A UUID used to group related commission calculations together.
- `column`: The column that was used to apply the commission (e.g., price, total).
- `original_amount`: The original amount before commission was applied.
- `calculated_amount`: The amount after the commission was calculated.
- `commission_amount`: The actual commission amount applied to the original amount.
- `status`: The status of the commission calculation. You must use the `\Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryStatusEnum` to define possible statuses (e.g., success, error).
- `reason`: The reason for the commission calculation outcome (optional). You must use the `\Mkeremcansev\LaravelCommission\Enums\CommissionCalculateHistoryReasonEnum` to define the possible reasons.
- `created_at`: Timestamp indicating when the commission calculation was recorded.

## Usage

1. **Implement the Interface**: Ensure that the model you want to apply commissions to implements the `HasCommissionInterface`. This interface defines the methods required for commission calculations.

2. **Use the Trait**: Use the `HasCommission` trait within your model to gain access to the commission calculation methods.

3. **Implement the Method**: Implement the `getCommissionableColumns()` method in your model to specify which columns can have commissions applied.

   ```php
   use Mkeremcansev\LaravelCommission\Contracts\HasCommissionInterface;
   use Mkeremcansev\LaravelCommission\Traits\HasCommission;

   class YourModel extends Model implements HasCommissionInterface
   {
       use HasCommission; // Use the HasCommission trait

       // Implement the required method from the HasCommissionInterface
       public function getCommissionableColumns(): array
       {
           return ['price', 'total']; // Example columns that can have commissions applied
       }
   }
   ```


## Testing

```bash
composer test
```

## Credits

- [Mustafa Kerem CANSEV](https://github.com/mkeremcansev)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
