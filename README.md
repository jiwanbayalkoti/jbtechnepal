# Product Comparison System

A web application that allows users to compare electronic products like laptops, PCs, and mobile phones by their specifications.

## Features

- Product categorization (laptops, PCs, mobile phones, etc.)
- Detailed product specifications
- Side-by-side product comparison
- Admin panel for managing products, categories, and specifications
- Responsive design for mobile and desktop

## Technologies Used

- Laravel 10.x
- MySQL
- jQuery
- Bootstrap 5
- Font Awesome
- AJAX

## Requirements

- PHP >= 8.1
- Composer
- MySQL
- Node.js and NPM (optional, for frontend assets)
- XAMPP or similar local development environment

## Installation

1. Clone the repository:
   ```
   git clone https://github.com/yourusername/product-compare.git
   cd product-compare
   ```

2. Install dependencies:
   ```
   composer install
   ```

3. Create a copy of your .env file:
   ```
   cp .env.example .env
   ```

4. Configure your database in the .env file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=product_compare
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Generate an application key:
   ```
   php artisan key:generate
   ```

6. Create a symbolic link for storage:
   ```
   php artisan storage:link
   ```

7. Run the database migrations:
   ```
   php artisan migrate
   ```

8. Start the local development server:
   ```
   php artisan serve
   ```

9. Visit http://localhost:8000 in your browser.

## Usage

### Admin Panel

Access the admin panel at `/admin` to:
- Manage product categories
- Add specification types for each category
- Add products with detailed specifications
- Edit and delete existing data

### Frontend

- Browse product categories on the homepage
- View products within a category
- Compare multiple products side by side
- View detailed product specifications

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
