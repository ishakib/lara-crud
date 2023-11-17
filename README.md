<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
  <a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# LaraCrud

LaraCrud is a Laravel package designed to simplify the process of generating complete CRUD (Create, Read, Update, Delete) operations in your Laravel applications. With LaraCrud, you can quickly scaffold the necessary files for a CRUD module, allowing you to focus on building your application's unique features.

## Installation

### Step 1: Install LaraCrud

You can install LaraCrud via Composer. Open a terminal window and run the following command:

```
composer require ishakib/lara-crud
```

**Step 2:**  Uncomment laracrud\LaraCrudServiceProvider::class from the providers array in the config/app.php file:
```
laracrud\LaraCrudServiceProvider::class,
//App\Providers\RepositoryRegisterProvider::class
```

**Step 3:** Run the following command to publish the Laravel CRUD assets:

```
php artisan vendor:publish --tag=laracrud-publish
```

**Step 4:**  Uncomment App\Providers\RepositoryRegisterProvider::class from the providers array in the config/app.php file:
```
App\Providers\RepositoryRegisterProvider::class
```
**Step 5:** The providers array in the config/app.php file should look like:
```
laracrud\LaraCrudServiceProvider::class,
App\Providers\RepositoryRegisterProvider::class
```
Now, your Laravel project is integrated with the CRUD Generator. Follow these steps for a smooth setup.

Now Enjoy the crud cmd from terminal
```
php artisan lara:crud
```
After creating all desired crud you need to migration
```
php artisan nigrate:fresh
```
##Features
Generate View Files: Automatically create views for your CRUD operations.
Generate Controller: Generate a controller with CRUD methods.
Generate Model: Create an Eloquent model for your resource.
Generate Validation Files: Automatically generate validation files for form requests.
Generate Migration Files: Create database migration files for your resource.
Generate Routes: Add CRUD routes to your web.php file.

## Configuration
Customize LaraCrud by editing the configuration file located at config/lara-crud.php. Adjust the settings according to your project requirements.

## License
This package is open-sourced software licensed under the MIT license.

Author
A K M Shakib Hossain
Contributions are welcome! If you find any issues or have suggestions for improvement, feel free to open an issue or create a pull request.

Happy coding!
