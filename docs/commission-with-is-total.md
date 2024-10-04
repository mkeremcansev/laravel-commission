# Commission with is_total (Integer Calculations)

In this example, a product is priced at 10000 (100.00$). Two commissions are defined. The first commission is a percentage-based commission calculated as an integer. The second commission, with `is_total` set to `true`, calculates its amount based on the total of the original price and previously calculated commissions.

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
        'description' => 'Commission with is_total (using integers)'
    ]);

$commissionType->commissionTypeModels()
    ->create([
        'model_type' => Product::class,
        'model_id' => null
    ]);

// Commission 1: Percentage commission (APPLIED)
$commissionType->commissions()->create([
    'rate' => 16, // 16%
    'amount' => null,
    'status' => true,
    'is_total' => false, // Not total
    'type' => \Mkeremcansev\LaravelCommission\Enums\CommissionTypeEnum::PERCENTAGE,
    'rounding' => \Mkeremcansev\LaravelCommission\Enums\CommissionRoundingEnum::UP,
    'order' => 1
]);

// Commission 2: Total commission (APPLIED based on the total of the original price and previous commissions)
$commissionType->commissions()->create([
    'rate' => 10, // 10%
    'amount' => null,
    'status' => true,
    'is_total' => true, // Total commission
    'type' => \Mkeremcansev\LaravelCommission\Enums\CommissionTypeEnum::PERCENTAGE,
    'rounding' => \Mkeremcansev\LaravelCommission\Enums\CommissionRoundingEnum::UP,
    'order' => 2
]);

$product->calculate('price');
```

**Calculation:**

- **Commission 1:** 10000 * 16% = 1600
- **Total before Commission 2:** Original Price (10000) + Commission 1 (1600) = 11600
- **Commission 2:** 11600 * 10% = 1160 → Rounded up to **1160**

**Final Result:**  
Product Price (10000) + Commission 1 (1600) + Commission 2 (1160) = **12760**
