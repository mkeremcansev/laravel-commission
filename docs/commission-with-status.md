# Commission with Status: Active and Inactive

In this example, two commissions are created. Only the commission with an active status (`status: true`) will be applied to the product price. Any commission with `status: false` is ignored.

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
        'description' => 'Commission with status control'
    ]);

$commissionType->commissionTypeModels()
    ->create([
        'model_type' => Product::class,
        'model_id' => null
    ]);

// Commission 1: Percentage commission (APPLIED, because status = true)
$commissionType->commissions()->create([
    'rate' => 20.00,
    'amount' => null,
    'status' => true, // Active commission
    'type' => \Mkeremcansev\LaravelCommission\Enums\CommissionTypeEnum::PERCENTAGE,
    'rounding' => \Mkeremcansev\LaravelCommission\Enums\CommissionRoundingEnum::UP,
    'order' => 1
]);

// Commission 2: Fixed commission (NOT APPLIED, because status = false)
$commissionType->commissions()->create([
    'rate' => null,
    'amount' => 10000, // 10000 = 100.00$
    'status' => false, // Inactive commission
    'type' => \Mkeremcansev\LaravelCommission\Enums\CommissionTypeEnum::FIXED,
    'rounding' => \Mkeremcansev\LaravelCommission\Enums\CommissionRoundingEnum::UP,
    'order' => 2
]);

$product->calculate('price');
```

**Calculation:**

- The first commission has an active status (`status: true`), so it is applied.
- The second commission is inactive (`status: false`), so it is ignored.

**Final Result:**  
Product Price (10000) + Percentage Commission (2000) = **12000**
