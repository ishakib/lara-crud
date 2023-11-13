# LaraCrud

LaraCrud is a Laravel package designed to simplify the process of generating complete CRUD (Create, Read, Update, Delete) operations in your Laravel applications. With LaraCrud, you can quickly scaffold the necessary files for a CRUD module, allowing you to focus on building your application's unique features.

## Installation

You can install LaraCrud via Composer. Open a terminal window and run the following command:

```bash
composer require ishakib/lara-crud

After installation, publish the package assets to customize the configuration:

php artisan vendor:publish --tag=lara-crud

Usage
Once installed, you can use the LaraCrud Artisan command to generate a complete CRUD module. Here's a simple example:

php artisan lara-crud:generate Example

Features
Generate View Files: Automatically create views for your CRUD operations.
Generate Controller: Generate a controller with CRUD methods.
Generate Model: Create an Eloquent model for your resource.
Generate Validation Files: Automatically generate validation files for form requests.
Generate Migration Files: Create database migration files for your resource.
Generate Routes: Add CRUD routes to your web.php file.

Configuration
Customize LaraCrud by editing the configuration file located at config/lara-crud.php. Adjust the settings according to your project requirements.

License
This package is open-sourced software licensed under the MIT license.

Author
Your Name
Contribution
Contributions are welcome! If you find any issues or have suggestions for improvement, feel free to open an issue or create a pull request.

Happy coding!
