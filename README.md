## Verde API
<span>by Wildan Maulana</span>
This project is hosted in https://verde-api.wildan.xyz

## About Project

This project developed with Laravel 9, contain RESTful API for an escape room booking system.

## How to Deploy

1. Git clone <code>git clone https://https://github.com/wildanku/verde-api</code>
2. Copy .env.example and configure your database <code>cp .env.example .env</code>
3. Composer Install <code>comopser install</code>
4. Run Laravel key generate <code>php artisan key:generate</code>
5. Database migration <code>php artisan migrate</code>
6. Run your Laravel <code>php artisan serve</code>
7. For testing the API run command <code>php artisan test</code>

## API Documentation
You can see the detail of API in this link https://www.postman.com/wildan-maulana-playground/workspace/verde-api

### Base URL (Development Server)
https://verde-api.wildan.xyz

### User Registration

```http
GET /user/auth/registration
```

| Parameter | Type | Description |
| :--- | :--- | :--- |
| `name` | `string` | **required|string|min:3|max:100**.|
| `email` | `string` | **required|email|unique**.|
| `phone` | `string` | **required|min:6|max:20|unique**.|
| `birth_date` | `string` | **required|date|before:now**.|
| `password` | `string` | **required|string|min:6|max:55|confirmed**.|
| `password_confirmation` | `string` | **required|string|min:6|max:55|confirmed**.|

