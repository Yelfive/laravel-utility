# Utilities
Making laravel more practical
### Database
##### Model

- extends `fk\utility\Database\Eloquent\Model`
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

- extends `fk\utility\Http\Request`
- functionality
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

At last, you should set the `config/session.php` add
```
'auto_start' => true,
```