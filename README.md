# Utilities
Making laravel more practical
### Database
##### Model

- Usage: Just extends `fk\utility\Database\Eloquent\Model`
- functionality
    - Add approach to get the sql to be executed.
        It's available by calling
        ```php
        <?php

        use fk\utility\Database\Eloquent\Model;
        $model = Model::find(1);
        $model->rawSql();
        // or simply call, witch applies the __toString method
        echo $model;
        ```
        In fact, it works for any method that returns a `fk\utility\Database\Eloquent\Builder`
#### Request

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
        $this->app->alias('request', fk\utility\Http\Request::class);
    }
    ```
- Functionality
    - Add support for Content-Type `multipart/form-data` for method `PUT`

### Session
Allow session to be actually applied just when called. Not when requested.
This is useful for RESTFul APIs, for some doesn't need a session.
```
# AppServiceProvider
public function register()
{
    $this->app->register(fk\utility\Session\SessionServiceProvider::class);
}
# or add to config/app.php

'providers' => [
    fk\utility\Session\SessionServiceProvider::class
]
```
Also remember not to define the `\Illuminate\Session\SessionServiceProvider`

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

# Easy Authentication

#### Register Service Provider

`fk\utility\Auth\Session\SessionGuardServiceProvider`

#### Config
```
# auth.php

return [
    'guards' => [
        'api' => [
            'driver' => 'easy.token',
            'model' => \App\Models\User::class, // The model to retrieve user from
        ]
    ]
];
```
