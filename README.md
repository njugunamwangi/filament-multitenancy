## About Project

A simple multi tenancy project built with Laravel & FilamentPHP

# Tech Stack

- [Laravel](https://laravel.com).
- [Livewire](https://livewire.laravel.com).
- [TailwindCSS](https://tailwindcss.com).
- [FilamentPHP V3](https://filamentphp.com).

## Installation guide

- Clone the repository

```bash
git clone https://github.com/njugunamwangi/filament-multitenancy.git
```
- On the root of the directory, copy and paste .env.example onto .env and configure the database accordingly
 ```bash
copy .env.example .env
```

- Run migrations and seed the database
```bash
php artisan migrate --seed
```

- Install composer dependencies by running composer install
 ```bash
composer install
```

- Install npm dependencies
```bash
npm install
```

- Generate laravel application key using 
```bash
php artisan key:generate
```

# Routes

- [Admin Panel](https://admin.filament-multitenancy.test.com).
- [App Panel](https://app.filament-multitenancy.test.com).

## Prerequisites

- Admin panel credentials

```bash
email: info@ndachi.dev
password: Password
```

- App panel credentials

```bash
email: owner1@filament.test
password: Password
```
