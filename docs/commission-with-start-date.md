# Commission with Start Date

In this example, two commissions are applied to a product. One of the commissions has a future `start_date`, and since the current date is before that date, it will not be applied. Only the active commission is included in the calculation.

## Example

```php
$product = Product::query()
    ->create([
        'name' => 'Product 1',
        'price' => 10000 // 10000 = 100.00$
    ]);

$commissionType = \Mkeremcansev\LaravelCommission\Models\CommissionType::query()
    ->create([
        'name' => 'VAT + DEALER COMMISSION',
        'description' => 'Commission with a future start date'
    ]);

$commissionType->commissionTypeModels()
    ->create([
        'model_type' => Product::class,
        'model_id' => null
    ]);

// Commission 1: Percentage commission (FUTURE, NOT APPLIED)
$commissionType->commissions()->create([
    'rate' => 20.00,
    'amount' => null,
    'type' => \Mkeremcansev\LaravelCommission\Enums\CommissionTypeEnum::PERCENTAGE,
    'start_date' => now()->addDays(10),  // Will start 10 days from now
    'end_date' => null,                  
    'rounding' => \Mkeremcansev\LaravelCommission\Enums\CommissionRoundingEnum::UP,
    'order' => 1
]);

// Commission 2: Fixed commission (ACTIVE)
$commissionType->commissions()->create([
    'rate' => null,
    'amount' => 10000, // 10000 = 100.00$
    'type' => \Mkeremcansev\LaravelCommission\Enums\CommissionTypeEnum::FIXED,
    'rounding' => \Mkeremcansev\LaravelCommission\Enums\CommissionRoundingEnum::UP,
    'order' => 2,
    'start_date' => now()->subDays(10),  // Started 10 days ago
    'end_date' => null                   
]);

$product->calculate('price');
```

**Calculation:**

- The percentage commission (20%) is not applied because its `start_date` is in the future.
- The fixed commission of 100.00$ is applied.

**Final Result:**  
Product Price (10000) + Fixed Commission (10000) = **20000**
