# Commission with Min Amount

In this example, two commissions are applied to a product, but only the commissions where the product price is greater than or equal to the specified `min_amount` will be included. Commissions that don't meet this condition are ignored.

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
        'description' => 'Commission with minimum amount restriction'
    ]);

$commissionType->commissionTypeModels()
    ->create([
        'model_type' => Product::class,
        'model_id' => null
    ]);

// Commission 1: Percentage commission (APPLIED, because price >= min_amount)
$commissionType->commissions()->create([
    'rate' => 20.00,
    'amount' => null,
    'min_amount' => 5000,  // Product price must be at least 50.00$
    'type' => \Mkeremcansev\LaravelCommission\Enums\CommissionTypeEnum::PERCENTAGE,
    'rounding' => \Mkeremcansev\LaravelCommission\Enums\CommissionRoundingEnum::UP,
    'order' => 1
]);

// Commission 2: Fixed commission (NOT APPLIED, because price < min_amount)
$commissionType->commissions()->create([
    'rate' => null,
    'amount' => 10000, // 10000 = 100.00$
    'min_amount' => 15000, // Product price must be at least 150.00$
    'type' => \Mkeremcansev\LaravelCommission\Enums\CommissionTypeEnum::FIXED,
    'rounding' => \Mkeremcansev\LaravelCommission\Enums\CommissionRoundingEnum::UP,
    'order' => 2
]);

$product->calculate('price');
```

**Calculation:**

- The product price is 10000, which meets the `min_amount` condition for the first commission (5000), so it's applied.
- The second commission is ignored because the product price is lower than the required `min_amount` of 15000.

**Final Result:**  
Product Price (10000) + Percentage Commission (2000) = **12000**  
