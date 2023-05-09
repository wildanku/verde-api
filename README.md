## Verde API
Source Code by Wildan Maulana <br>
This project is hosted in https://verde-api.wildan.xyz

## About Project

This project developed with Laravel 9, contain RESTful API for an escape room booking system.

## How to Deploy

1. Git clone <code>git clone https://github.com/wildanku/verde-api</code>
2. Copy .env.example and configure your database <code>cp .env.example .env</code>
3. Composer Install <code>comopser install</code>
4. Run Laravel key generate <code>php artisan key:generate</code>
5. Database migration <code>php artisan migrate</code>
6. Run your Laravel <code>php artisan serve</code>
7. For testing the API run command <code>php artisan test</code>

## API Documentation
You can see the detail of API in this link https://www.postman.com/wildan-maulana-playground/workspace/verde-api

### Base URL (Development Server)
https://verde-api.wildan.xyz <br>
(dummy data is available in development server)

### User Registration

```http
POST /user/auth/registration
```

| Parameter | Type | Description |
| :--- | :--- | :--- |
| `name` | `string` | required, string, min:3|max:100 |
| `email` | `email` | required, email, unique |
| `phone` | `string` | required, min:6, max:20, unique |
| `birth_date` | `date` | required, date, before:now |
| `password` | `string` | required, string, min:6, max:55, confirmed |
| `password_confirmation` | `string` | required, string, min:6, max:55, confirmed |

### User Authentication

```http
POST /user/auth/login
```

| Parameter | Type | Description |
| :--- | :--- | :--- |
| `email` | `email` | required, email|
| `password` | `string` | required, string, min:6, max:55|


### Get Escape Room

```http
GET /user/find/rooms
```

| Parameter | Type | Description |
| :--- | :--- | :--- |
| `name` | `string` | sometimes, nullable, min:1, max:55 |
| `theme` | `string` | sometimes, nullable, min:1, max:55 |
| `pax` | `numeric` | sometimes, nullable, numeric, min:1, max:99 |
| `checkin` | `date` | sometimes, nullable, date, after_or_equal:today |
| `checkout` | `date` | sometimes, nullbale, date, after:checkin |
| `offset` | `numeric` | sometimes, nullable, min:1, max:100 |
| `page` | `numeric` | sometimes, nullable, min:1 |


### Get Detail Escape Room

```http
GET /user/find/room/{room}
```


### Book Escape Room

To book, you should provide your API key in the `Authorization` header.

```http
POST /user/booking/create
```

| Parameter | Type | Description |
| :--- | :--- | :--- |
| `room_id` | `numeric` | required, exists:rooms,id .|
| `pax` | `numeric` | sometimes, nullable, numeric, min:1, max:99|
| `checkin` | `date` | sometimes, nullable, date, after_or_equal:today |
| `checkout` | `date` | sometimes, nullbale, date, after:checkin |
| `notes` | `string` | sometimes, min:3, max:255 |


### Show all Bookings

To see all bookings, you should provide your API key in the `Authorization` header.

```http
GET /user/booking/all
```

### Show Booking Detail

To see detail of booking, you should provide your API key in the `Authorization` header.

```http
GET /user/booking/{booking}
```


### Cancel Booking

To cancel booking, you should provide your API key in the `Authorization` header.

```http
GET /user/booking/{booking}/cancel
```




