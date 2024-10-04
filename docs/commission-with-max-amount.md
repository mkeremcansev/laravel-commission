# Commission with Max Amount

In this example, two commissions are applied to a product, but only the commissions where the product price is less than or equal to the specified `max_amount` will be included. Commissions that exceed this limit are ignored.

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
        'description' => 'Commission with maximum amount restriction'
    ]);

$commissionType->commissionTypeModels()
    ->create([
        'model_type' => Product::class,
        'model_id' => null
    ]);

// Commission 1: Percentage commission (APPLIED, because price <= max_amount)
$commissionType->commissions()->create([
    'rate' => 20.00,
    'amount' => null,
    'max_amount' => 15000,  // Product price must be at most 150.00$
    'type' => \Mkeremcansev\LaravelCommission\Enums\CommissionTypeEnum::PERCENTAGE,
    'rounding' => \Mkeremcansev\LaravelCommission\Enums\CommissionRoundingEnum::UP,
    'order' => 1
]);

// Commission 2: Fixed commission (NOT APPLIED, because price > max_amount)
$commissionType->commissions()->create([
    'rate' => null,
    'amount' => 10000, // 10000 = 100.00$
    'max_amount' => 9000,  // Product price must be at most 90.00$
    'type' => \Mkeremcansev\LaravelCommission\Enums\CommissionTypeEnum::FIXED,
    'rounding' => \Mkeremcansev\LaravelCommission\Enums\CommissionRoundingEnum::UP,
    'order' => 2
]);

$product->calculate('price');
```

**Calculation:**

- The product price is 10000, which is less than or equal to the `max_amount` for the first commission (15000), so it's applied.
- The second commission is ignored because the product price exceeds the required `max_amount` of 9000.

**Final Result:**  
Product Price (10000) + Percentage Commission (2000) = **12000**  
