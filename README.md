
<picture>
  <source media="(prefers-color-scheme: dark)" srcset="https://banners.beyondco.de/Laravel%20Commission.png?theme=dark&packageManager=composer+require&packageName=mkeremcansev%2Flaravel-commission&pattern=architect&style=style_1&description=A+flexible+package+to+calculate+and+log+commissions+in+Laravel.&md=1&showWatermark=1&fontSize=100px&images=receipt-tax">
  <source media="(prefers-color-scheme: light)" srcset="https://banners.beyondco.de/Laravel%20Commission.png?theme=light&packageManager=composer+require&packageName=mkeremcansev%2Flaravel-commission&pattern=architect&style=style_1&description=A+flexible+package+to+calculate+and+log+commissions+in+Laravel.&md=1&showWatermark=1&fontSize=100px&images=receipt-tax">
  <img alt="Package Image" src="https://banners.beyondco.de/Laravel%20Commission.png?theme=light&packageManager=composer+require&packageName=mkeremcansev%2Flaravel-commission&pattern=architect&style=style_1&description=A+flexible+package+to+calculate+and+log+commissions+in+Laravel.&md=1&showWatermark=1&fontSize=100px&images=receipt-tax">
</picture>


## What is Laravel Commission?

Laravel Commission is a powerful package designed to simplify the management and calculation of commissions within Laravel applications. This package provides a flexible and extensible system for defining various types of commissions, whether they are based on percentages, fixed amounts, or more complex criteria.

### Key Features:

1. **Multiple Commission Types**: Laravel Commission allows you to define various commission types, such as percentage-based, fixed amount, or a combination of both. This flexibility enables developers to tailor commission structures to meet specific business requirements.

2. **Dynamic Calculation**: The package supports dynamic commission calculations based on the original product price or total price after applying other commissions. It accommodates different scenarios through the use of parameters such as `is_total`, `min_amount`, `max_amount`, and `rounding`.

3. **Comprehensive History Tracking**: Laravel Commission provides functionality to track the history of commission calculations, allowing businesses to maintain an accurate record of commissions applied over time.

4. **Integration with Eloquent Models**: The package seamlessly integrates with Laravel's Eloquent ORM, enabling developers to associate commissions with various models, such as products, orders, or services.

5. **Extensibility and Customization**: Developers can easily extend the functionality of the package to create custom commission logic that suits their business needs. This makes it easy to adapt to different use cases and scenarios.

### Use Cases:

- **E-commerce Platforms**: Laravel Commission is particularly useful for e-commerce applications where various commission rates need to be applied based on different products, sales channels, or user roles.

- **Affiliate Programs**: Businesses can manage affiliate commissions efficiently, calculating payouts based on sales generated by affiliates with flexible commission structures.

- **Sales Teams**: Organizations can implement commission structures for sales representatives, rewarding them based on their performance and sales targets.

In summary, Laravel Commission is a versatile and robust solution for managing commissions in Laravel applications, providing developers with the tools they need to implement and customize commission calculations effectively. Whether you're building an e-commerce site, an affiliate platform, or a sales management system, this package can help streamline your commission management processes.


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
   
4. **Calculate Commissions**: Use the `calculate('price')` method to apply commissions to your model.

   ```php
   $model = YourModel::find(1);
   $calculatedCommission = $model->calculate('price');
   $calculatedCommission->totalCommissionAmount; // The total commission amount applied
   $calculatedCommission->totalIncludedPreviousCommissionAmount // The total amount including previous commissions
   $calculatedCommission->totalAmount; // The total amount after commissions
   $calculatedCommission->originalAmount; // The original amount before commissions
   ```

###  Important Note

The `calculate()` method returns a `\Mkeremcansev\LaravelCommission\Services\Contexts\CommissionCalculationResultContext` or `array` or `null`.

**Null**: If the model `getCommissionableColumns()` method is return a empty array.

**Array**: If the model `getCommissionableColumns()` method has multiple columns. (Return array of `\Mkeremcansev\LaravelCommission\Services\Contexts\CommissionCalculationResultContext`)

**CommissionCalculationResultContext**: If the model `getCommissionableColumns()` method has only one column.

**### If you want a commission not to be calculated after a specific date, refer to the following documentation:**

- **[Understand the process of defining a start date for a commission, which controls when the commission calculations begin.](docs/commission-with-start-date.md)**


- **[Learn how to set an end date for a commission to prevent further calculations after that date.](docs/commission-with-end-date.md)**


- **[Understand how the is_total column aggregates all applicable commission amounts for a more comprehensive total.](docs/commission-with-is-total.md)**


- **[Discover how to apply a maximum amount limit for commission calculations based on specified criteria.](docs/commission-with-max-amount.md)**


- **[Explore how minimum amount checks are applied based on product price, ensuring that commissions are calculated only when the price meets the minimum criteria.](docs/commission-with-min-amount.md)**


- **[Find out how the rounding down method impacts commission calculations and ensures the amounts are adjusted downwards.](docs/commission-with-rounding-down.md)**


- **[Learn how the rounding up method impacts commission calculations and ensures the amounts are adjusted upwards.](docs/commission-with-rounding-up.md)**



- **[Discover how the commission status (true/false) affects whether a commission is included in calculations.](docs/commission-with-status.md)**


- **[Learn about the default commission calculation process and its importance in the overall commission framework.](docs/default-commission-calculation.md)**


## Testing

```bash
composer test
```

## Credits

- [Mustafa Kerem CANSEV](https://github.com/mkeremcansev)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
