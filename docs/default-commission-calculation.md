# Default Commission Calculation

In this example, a product is created with a price of 100.00$. Two commissions are applied:

1. **Percentage Commission (20%)**: Adds a 20% commission to the product price.
2. **Fixed Commission (100.00$)**: Adds a fixed amount commission.

```php
$product = Product::query()
    ->create([
        'name' => 'Product 1',
        'price' => 10000 // 10000 = 100.00$
    ]);

$commissionType = \Mkeremcansev\LaravelCommission\Models\CommissionType::query()
    ->create([
        'name' => 'VAT + DEALER COMMISSION',
        'description' => 'Percentage based commission'
    ]);

$commissionType->commissionTypeModels()
    ->create([
        'model_type' => Product::class,
        'model_id' => null
    ]);

$commissionType->commissions()->create([
    'rate' => 20.00,
    'amount' => null,
    'type' => \Mkeremcansev\LaravelCommission\Enums\CommissionTypeEnum::PERCENTAGE,
    'rounding' => \Mkeremcansev\LaravelCommission\Enums\CommissionRoundingEnum::UP,
    'order' => 1
]);

$commissionType->commissions()->create([
    'rate' => null,
    'amount' => 10000, // 10000 = 100.00$
    'type' => \Mkeremcansev\LaravelCommission\Enums\CommissionTypeEnum::FIXED,
    'rounding' => \Mkeremcansev\LaravelCommission\Enums\CommissionRoundingEnum::UP,
    'order' => 2
]);

$product->calculate('price');
```

**Calculation:**

1. Percentage Commission: 10000 * 20% = 2000
2. Fixed Commission: 10000

**Final Result:**  
Product Price (10000) + Commission 1 (2000) + Commission 2 (10000) = **22000**  
