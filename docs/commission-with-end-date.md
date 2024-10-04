# Commission with End Date

In this example, two commissions are applied to a product. One of the commissions has an `end_date`, and since the current date is beyond that date, it will not be applied. Only the active commission will be included in the final calculation.

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
        'description' => 'Commission with an expired date'
    ]);

$commissionType->commissionTypeModels()
    ->create([
        'model_type' => Product::class,
        'model_id' => null
    ]);

// Commission 1: Percentage commission (EXPIRED)
$commissionType->commissions()->create([
    'rate' => 20.00,
    'amount' => null,
    'type' => \Mkeremcansev\LaravelCommission\Enums\CommissionTypeEnum::PERCENTAGE,
    'start_date' => now()->subDays(60), // Started 60 days ago
    'end_date' => now()->subDays(30),   // Expired 30 days ago
    'rounding' => \Mkeremcansev\LaravelCommission\Enums\CommissionRoundingEnum::UP,
    'order' => 1
]);

// Commission 2: Fixed commission (NON-EXPIRED)
$commissionType->commissions()->create([
    'rate' => null,
    'amount' => 10000, // 10000 = 100.00$
    'type' => \Mkeremcansev\LaravelCommission\Enums\CommissionTypeEnum::FIXED,
    'rounding' => \Mkeremcansev\LaravelCommission\Enums\CommissionRoundingEnum::UP,
    'order' => 2,
    'start_date' => now(),  // Active from now
    'end_date' => null      // No expiration
]);

$product->calculate('price');
```

**Calculation:**

- The percentage commission (20%) is not applied because its `end_date` has passed.
- The fixed commission of 100.00$ is applied.

**Final Result:**  
Product Price (10000) + Fixed Commission (10000) = **20000**  
