# Utilities

Making laravel more practical

## Database

### Model

- Usage: Just extends `fk\utility\Database\Eloquent\Model`
- functionality
    - Add approach to get the sql to be executed.
        It's available by calling
        ```php
        <?php

        use fk\utility\Database\Eloquent\Model;
        /** @var  \fk\utility\Database\Eloquent\Builder $model */
        $model = Model::find(1);
        $model->rawSql();
        // or simply call, witch applies the __toString method
        echo $model;
        ```
        In fact, it works for any method that returns a `fk\utility\Database\Eloquent\Builder`

    - Modify pagination

        - add access to custom fields when calling `toArray`
        - add access to `toFKStyle`

    - Model::select related

        > Being able to using alias like following,
            see `\fk\utility\Database\Query\Builder::select` for more

        ```php
        <?php

          \fk\utility\Database\Eloquent\Model::select(['alias' => ['fields']]);
        ```

## Request

- Class
    - `fk\utility\Http\Request`
- Usage
    - Capture at `public/index.php`
    ```
    # index.php, replace the default capture
    $response = $kernel->handle(
        $request = \fk\utility\Http\Request::capture()
    );
    ```
    - Extends or use it for IOC
    - Register it's alias as `request`,
      to ensure every instance fallback to the singleton instance
      used to capture at entry index.php
    ```#
    # AppServiceProvider.php
    public function reigster()
    {
        $this->app->alias('request', \fk\utility\Http\Request::class);
    }
    ```
- Functionality
    - Add support for Content-Type `multipart/form-data` for method `PUT`

## Session

Allow session to be actually applied just when called. Not when requested.
This is useful for RESTFul APIs, for some doesn't need a session.

###  AppServiceProvider

```php
<?php

class AppServiceProvider {
    
    public function register()
    {
        $this->app->register(\fk\utility\Session\SessionServiceProvider::class);
    }
}
```

### or add to config/app.php

```php
<?php

[
    'providers' => [
        fk\utility\Session\SessionServiceProvider::class
    ]
];
```

Also remember cancel registering of the `\Illuminate\Session\SessionServiceProvider`

At last, you should set the `config/session.php` add

```
'auto_start' => true,
```

Also, remember to disable Laravel's start-on-every-request feature
by comment the following if exists

```
# app\Http\Kernel

public $middlewares = [
//    \Illuminate\Session\Middleware\StartSession::class,
]
```

If you have your own rule of session id,
you can overwrite the `\fk\utility\Session\SessionServiceProvider::getAccessToken`
to achieve that

## Easy Authentication

### Register Service Provider

`fk\utility\Auth\Session\SessionGuardServiceProvider`

which is totally independent of `SessionServiceProvider`


```php
<?php

class AppServiceProvider
{
    public function register()
    {
        $this->app->register(fk\utility\Auth\Session\SessionGuardServiceProvider::class);
    }
}

```


### Config

```php
<?php

# auth.php

return [
    'guards' => [
        'api' => [
            'driver' => 'easy.token',
            'model' => \App\Models\User::class, // The model to retrieve user from
            'checkExists' => false, // whether to check `count` in database on every request
        ]
    ]
];
```

Then you are good to go.

This will automatically enable to retrieve token from header `X-Access-Token` as session id

## PHPUnit

### TestCase

- Class: `fk\utility\Foundation\Testing\TestCase`
- Benefits: Output for json would be
            human readable for Chinese characters
- Usage:

    ```php
    <?php
    
    use \fk\utility\Foundation\Testing\TestCase;
    
    class YourTest extends TestCase
    {
        // Write your own `CreateApplication`
        // OR
        // Write a `createApplication` method here
        use CreateApplication;
    }
    ```
## ACL Check

- Class: `fk\utility\Auth\Middleware\AclAuthenticate`
- Usages:

    - Create your own authentication class to place your rules

        ```php
        <?php
        
        namespace App\Http\Middleware;
        
        use fk\utility\Auth\Middleware\AclAuthenticate;
        
        class MyAuthenticate extends AclAuthenticate
        {
            public function authenticate(): bool
            {
                // Write your own authentication here
                // If false returned, a `Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException` exception will be thrown
                // otherwise, authentication will pass.
                // Feel free to throw any kind of exceptions that fits you
            }
        }
        ```

    - Register at `App\Http\Kernel`

        ```php
          <?php
        
          class Kernel
          {
        
              protected $routeMiddleware = [
                  'auth.acl' => \App\Http\Middleware\MyAuthenticate::class,
              ];
          }
        ```

    - Good to go. Define a route using middleware `auth.acl`

        ```php
          <?php
        
          Route::group(['middleware' => 'auth.acl'], function () {
              Route::get('sth', 'SomeController@someMethod');
              // ... stuff
          });
        
        ```
