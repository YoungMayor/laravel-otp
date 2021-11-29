# Very short description of the package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/youngmayor/laravel-otp.svg?style=flat-square)](https://packagist.org/packages/youngmayor/laravel-otp)
[![Total Downloads](https://img.shields.io/packagist/dt/youngmayor/laravel-otp.svg?style=flat-square)](https://packagist.org/packages/youngmayor/laravel-otp)
![GitHub Actions](https://github.com/youngmayor/laravel-otp/actions/workflows/main.yml/badge.svg)

This is where your description should go. Try and limit it to a paragraph or two, and maybe throw in a mention of what PSRs you support to avoid any confusion with users and contributors.

## Installation

You can install the package via composer:

```bash
composer require youngmayor/laravel-otp
```

The package uses auto discovery hence if you use Laravel 5.5 or above, installing the package automatically registers it in your application. 

However, if you use Laravel 5.4 or below you will need to add the below snipet to your `config/app.php` to register the Service Provider and alias
```php
'providers' => [
    // ...
    YoungMayor\LaravelOtp\LaravelOtpServiceProvider,
    // ...
],
```

Next step is to publish the configuration for the package. This can be done using the command below

```bash
php artisan vendor:publish --provider="YoungMayor\LaravelOtp\LaravelOtpServiceProvider" 
```

This copies the package configuration into `config/laravel-otp.php`

Then you run migration to create the OTP tables

```bash
php artisan migrate
```

## Usage

Models which intend to manage OTP codes should extend the `YoungMayor\LaravelOtp\Traits\OTPActions` trait 

```php
// ...
use YoungMayor\LaravelOtp\Traits\OTPActions;
// ...

class ExampleModel
{
    // ...
    use OTPActions;
    // ....
}
```
> It is required that the model posses an email field. This field would store the recipient of the generate OTP code. It is assumed that this field is named email. If the field has a different name, then it should be configured using the below code 
> ```php 
> class ExampleModel
> {
>   use OTPActions; 
>
>   public $otpEmailKey = 'user_email';
> }
> ```

OTPs can now be managed using that model. 

### OTP Creation
```php 
// $exampleModel is an instance of ExampleModel
// $payload is additional data that you would like to attach to the OTP token. 
// The payload can be an array, a string or an integer
$exampleModel->generatOTP('action-name', $payload);
```
This generates an OTP using the configuration defined in `config/laravel-otp.php` and emails the generate OTP to the email attached to the model

### OTP Validation 
```php 
// $exampleModel is an instance of ExampleModel and has a pending OTP
// $pin is the OTP pin that is to be validated
$otp = $exampleModel->validateOTP($pin, 'action-name');
```
If the pin is correct an instance of `YoungMayor\LaravelOtp\Models\OneTimePin` would be returned, else null would be returned 
```php 
if (!$otp) {
    // handle invalid OTP code
}
```

### Managing OTP 
The OTP can now be managed as below 
```php 
$otp->is_expired; // check if the OTP is expired

$payload = $otp->payload; // retrieve the OTP's Payload

$otp->refreshOTP(); // re-generate a new OTP code and send to the recipient

$otp->delete(); // delete the OTP
```

### Relationships
Every model which utilises the `OTPActions` trait have the `otps` relationship and can perform [laravel one to many](https://laravel.com/docs/8.x/eloquent-relationships#one-to-many) relationshp on it. 

Likewise, from the `YoungMayor\LaravelOtp\Models\OneTimePin` instance. You can retrieve the source model using the source relationship method 
```php 
$exampleModel = $otp->source;
```

## Exceptions
The package renders two exceptions:
- `YoungMayor\LaravelOtp\Exceptions\InvalidAction` exception: Thrown when an attempt to `generateOTP()` or `validateOTP()` is made with an action that is not registered in the `config/laravel-otp.php` file.

- `YoungMayor\LaravelOtp\Exceptions\InvalidSource` exception: Thrown when the given source does not use the `OTPActions` trait

## Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email youngmayor.dev@gmail.com instead of using the issue tracker.

## Credits

-   [Meyoron Aghogho](https://github.com/youngmayor)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
