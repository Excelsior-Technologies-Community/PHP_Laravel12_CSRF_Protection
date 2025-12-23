# PHP_Laravel12_CSRF_Protection

A practical **Laravel 12** project demonstrating **Cross-Site Request Forgery (CSRF) protection** in real-world scenarios. This repository explains how CSRF works in Laravel using protected forms, unsafe forms, and AJAX-based submissions with proper token handling.

---

## Project Overview

This project is designed to help developers understand how Laravel protects applications against CSRF attacks. It includes multiple examples to clearly show what happens **with** and **without** CSRF protection.

The project is suitable for:

* Laravel beginners
* Interview preparation
* Security concept demonstrations

---

## Key Features

* CSRF-protected form using Blade directive
* Unsafe form without CSRF token (demonstrates 419 error)
* AJAX form submission with CSRF token handling
* Server-side validation
* Bootstrap 5 responsive UI
* Clear logging of form submissions
* Demonstration of CSRF middleware behavior

---

## Prerequisites

Ensure the following are installed:

* PHP 8.1 or higher
* Composer
* MySQL (optional)
* Git

---

## Step-by-Step Setup

### Step 1: Create a New Laravel Project

```bash
composer create-project laravel/laravel laravel-csrf-demo
cd laravel-csrf-demo
```

---

### Step 2: Configure Database (Optional)

Edit the `.env` file:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_csrf
DB_USERNAME=root
DB_PASSWORD=
```

---

### Step 3: Define Routes

Edit `routes/web.php`:

```php
Route::get('/', function () {
    return view('welcome');
});

Route::get('/form', [FormController::class, 'showForm'])->name('form.show');
Route::post('/form', [FormController::class, 'submitForm'])->name('form.submit');

Route::get('/form-unsafe', [FormController::class, 'showUnsafeForm'])->name('form.unsafe.show');
Route::post('/form-unsafe', [FormController::class, 'submitUnsafeForm'])->name('form.unsafe.submit');

Route::get('/ajax-form', [FormController::class, 'showAjaxForm'])->name('ajax.form.show');
Route::post('/ajax-form', [FormController::class, 'submitAjaxForm'])->name('ajax.form.submit');
```

---

### Step 4: Create Controller

```bash
php artisan make:controller FormController
```

The controller handles:

* CSRF-protected form submission
* Unsafe form submission
* AJAX-based form submission

---

## Views Overview

### Main Layout

* Located at `resources/views/layouts/app.blade.php`
* Includes Bootstrap 5
* Contains CSRF token meta tag

```
<meta name="csrf-token" content="{{ csrf_token() }}">
```

---

### Protected Form

* Uses `@csrf` directive
* Submission succeeds
* Demonstrates Laravel token verification

```
<form method="POST">
    @csrf
</form>
```

---

### Unsafe Form

* No CSRF token
* Submission fails with **419 Page Expired** error
* Demonstrates CSRF attack prevention

---

### AJAX Form

* Uses jQuery AJAX
* CSRF token sent via request headers

```
X-CSRF-TOKEN: <token>
```

---

## CSRF Protection in AJAX

Laravel supports CSRF protection in AJAX requests using:

* Meta tag token
* X-CSRF-TOKEN header
* Hidden form input

The project demonstrates how to configure all methods correctly.

---

## Welcome Page

The home page explains:

* What CSRF is
* How Laravel prevents CSRF attacks
* Different ways to include CSRF tokens

It links to:

* Protected form
* Unsafe form
* AJAX form

---

## CSRF Middleware Configuration

Located at:

```
app/Http/Middleware/VerifyCsrfToken.php
```

Key options:

* Exclude specific URIs from CSRF protection
* Configure token expiration
* Enable or disable XSRF cookie

---

## Running the Application

```bash
php artisan key:generate
php artisan migrate
php artisan serve
```

Open in browser:

```
http://localhost:8000
```

---

## Testing Scenarios

* Protected form submits successfully
* Unsafe form returns 419 error
* AJAX form submits successfully
* Validation errors handled correctly

---

## CSRF Concepts Demonstrated

* Automatic token generation
* Token verification on POST requests
* Token mismatch handling
* Session-based CSRF tokens
* AJAX CSRF integration

---

## Security Best Practices

* Always use HTTPS in production
* Never expose CSRF tokens in URLs
* Regenerate tokens after authentication
* Avoid disabling CSRF globally
* Use SameSite cookies (default in Laravel)

---

## Screenshot

* Dashboard 
<img width="1784" height="971" alt="image" src="https://github.com/user-attachments/assets/f2ba32f7-b575-4f29-bcde-78369ef92886" />
* Protected Form
<img width="1613" height="809" alt="image" src="https://github.com/user-attachments/assets/75912638-eeb0-4755-8529-05178ade1445" />
* Unsafe Form
<img width="1673" height="923" alt="image" src="https://github.com/user-attachments/assets/84a04560-a5b9-48ed-8caf-c39eca81896a" />



## Final Note

This project clearly demonstrates Laravel's built-in CSRF protection mechanisms and how to use them correctly in traditional forms and AJAX-based applications.

It is ideal for learning, teaching, and security demonstrations in Laravel 12.
