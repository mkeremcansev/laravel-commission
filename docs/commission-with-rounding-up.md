# Commission with Rounding Up

In this example, a product is priced at 2517 (25.17$). A percentage-based commission of 16.50% is applied, and then a fixed commission of 1750 is added. The percentage-based commission is rounded up based on the rounding rule.

## Example

```php
$product = Product::query()
    ->create([
        'name' => 'Product 1',
        'price' => 2517 // 2517 = 25.17$
    ]);

$commissionType = \Mkeremcansev\LaravelCommission\Models\CommissionType::query()
    ->create([
        'name' => 'VAT + DEALER COMMISSION',
        'description' => 'Commission with rounding up'
    ]);

$commissionType->commissionTypeModels()
    ->create([
        'model_type' => Product::class,
        'model_id' => null
    ]);

// Commission 1: Percentage commission (APPLIED and rounded up)
$commissionType->commissions()->create([
    'rate' => 16.50,
    'amount' => null,
    'status' => true,
    'type' => \Mkeremcansev\LaravelCommission\Enums\CommissionTypeEnum::PERCENTAGE,
    'rounding' => \Mkeremcansev\LaravelCommission\Enums\CommissionRoundingEnum::UP, // Rounding Up
    'order' => 1
]);

// Commission 2: Fixed commission (APPLIED)
$commissionType->commissions()->create([
    'rate' => null,
    'amount' => 1750, // 1750 = 17.50$
    'status' => true,
    'type' => \Mkeremcansev\LaravelCommission\Enums\CommissionTypeEnum::FIXED,
    'rounding' => \Mkeremcansev\LaravelCommission\Enums\CommissionRoundingEnum::UP,
    'order' => 2
]);

$product->calculate('price');
```

**Calculation:**

- **Percentage Commission:** 2517 * 16.50% = 415.305 → Rounded up to **416**
- **Fixed Commission:** 1750 (17.50$)

**Final Result:**  
Product Price (2517) + Rounded Percentage Commission (416) + Fixed Commission (1750) = **4683**
