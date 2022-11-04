## PHP Framework

A simple mvc framework for building web applications.<br>
The framework, written using php8, has the basic features to create web applications, such as a routing system, a complete authentication system, a session management system and other features you can read about below. <br>
The framework is made entirely from scratch.
It only uses two packages: PHPmailer to manage the sending of emails, and `vlucas/phpdotenv`, a package to manage environment variables.<br><br>
Also, tailwind css cdn was used to style the project, but it can be safely removed as it is not necessarily needed.

&nbsp;

### Installation

-   Clone the project with "git clone https://github.com/Jordan-Bianco/mvc_framework.git"
-   Run the command `composer install`
-   Create the `.env` file and copy the contents of the `.env-example` file by entering your credentials
-   Run the `php migrations.php` command in the terminal to create all the tables in your database
-   Optionally you can run the command `php seed.php` to seed the database with a dummy user
-   Enter the public folder
-   Launch the server with the command `php -S localhost:8888`

&nbsp;

### :lock: Authentication

The framework has a complete authentication system. From user registration, to password recovery, to account deletion. <br>
All authentication-related routes can be found in the `routes` folder, in the `auth.php` file

> <small><strong>note</strong></small> The email for the account verification, and the check whether the user is verified or not (in the LoginController), are disabled by default.

-   User registration

    -   Password hash
    -   Send email to verify account
    -   Password reset

-   Login
    -   Session regeneration
-   Logout
-   Delete account

&nbsp;

### :round_pushpin: Router

Each route consists of two parameters.
The first parameter is the `url`, the second parameter can be an `array`, a `string` or a `callback`.
Depending on the case, the router will call the correct method.<br>
To register a route, (both get and post) the following syntax is used:

```php
$app->router->get('/', [PageController::class, 'home']);
$app->router->post('/login', [LoginController::class, 'login']);
$app->router->get('/', 'home');
$app->router->get('/', function() {
  return 'homepage';
});
```

To define a route with parameters, just define the parameter name in curly brackets.

```php
$app->router->get('/users/{id}', [UserController::class, 'show']);
```

&nbsp;

### :heavy_check_mark: Data validation

To validate the data from a form, the `Validation` class (which is extended by the `Request` class) is used.<br>
The array containing the form data, the rules specified for the various fields, and a string that refers to the view to be loaded in case of validation errors, are passed to the `validate` method.

```php
$rules = [
  'email' => ['required', 'email'],
  'password' => ['required'],
];
$validated = $request->validate($_POST, $rules, '/login');
```

If there are no validation errors, the `$validated` variable will contain the sanitized and validated data entered by the user.<br>
The list that refers to the validation rules, and related messages, is contained in the `Validation` class, in the `$availableRules` array.

```php
protected $availableRules = [
  'required' => 'Il campo :field: è obbligatorio.',
  'email' => 'Il campo :field: deve contenere un indirizzo email valido.',
  'alpha_dash' => 'Il campo :field: può contenere solo lettere, numeri, trattini e underscore.',
  ...
];
```

The rules where placeholders are contained, are specified as follows:

```php
'password_confirm' => ['required', 'match:password'],
```

&nbsp;

### :arrows_counterclockwise: Migrations

Each class inside the migrations folder contains an `up` method, through which it is possible to interact with the database.<br>
It is possible to create or delete tables, as well as add or remove fields from existing tables.<br>
The migrations system is managed within the `core\database` folder, in the `Database.php` file.<br>
To apply the various migrations, the `migrations.php` file is called from the terminal, to which one of the following flags can be passed:<br>

-   drop, which drops all the tables.
-   truncate, which removes all data within the tables.

&nbsp;

### :no_entry_sign: Middleware

Each middleware is registered inside the `core\middlewares` folder and must extend the `BaseMiddleware` class. <br>
Each new middleware must implement the `execute` method, within which the logic is implemented.<br>

```php
public function execute()
  {
    if (!Application::$app->session->isLoggedIn()) {
      Application::$app->response->redirect('/login');
    }
  }
```

To apply middleware to a controller, pass it in the constructor, and call the `registerMiddleware` method.<br>
It is also necessary to pass into the middleware constructor an array containing the methods on which to apply the middleware.

```php
public function __construct()
  {
    parent::__construct();

    $this->registerMiddleware(new GuestMiddleware(['login']));
  }
```

&nbsp;

### 👷 Query Builder

The Query Builder class is used to interact with the database and perform operations on it. This class has access to the PDO instance.
<br>
To run a query, you call the builder method on the app instance.
Then you can chain the methods to create queries, from the simplest to the most complex.<br>

> <small><strong>note</strong></small> Occasionally it is possible to pass raw queries, using the raw method.

```php
$user = $this->app->builder
  ->select()
  ->from('users')
  ->get();


$usersCount = Application::$app->builder
  ->count()
  ->from('users')
  ->getCount();


$users = Application::$app->builder
  ->select(['id', 'username'])
  ->from('users')
  ->where('id', $id)
  ->andWhere('verified', true)
  ->first();


$users = Application::$app->builder
  ->select(['users.id, users.username, count(posts.id) as post_count',])
  ->from('users')
  ->join('posts', 'user_id', 'users', 'id')
  ->groupBy('users.id')
  ->get();
```

&nbsp;

### :zap: Flash messages

The `withValidationErrors()` method is used to save flash messages in the session. <br>
This method accepts an array of errors.
The following code is located in the Validation.php class.

```php
Application::$app->response->redirect($url)
  ->withValidationErrors($this->errors)
  ->withOldData($data);
```

The `getValidationErrors()` method is used to show the errors to the user.
If no parameter is passed, the function will return all errors.

```php
<?php foreach (Application::$app->session->getValidationErrors() as $error) : ?>
```

If a parameter is passed, only the error relating to the passed parameter will be returned.

```php
<?= Application::$app->session->getValidationErrors('email') ?>
```

The `withOldData()` method, on the other hand, returns the user's inputs in case of an error. <br>
To return the data entered by the user in the form, in the event of an error, the following syntax is used.

```php
value = "<?= Application::$app->session->getOldData('email') >"
```
