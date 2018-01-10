# easy.token

## Register service

```php
<?php

/**
 * @property Illuminate\Contracts\Foundation\Application $app
 */
class AppServiceProvider
{
    public function register()
    {
        $this->app->register(\fk\utility\Auth\Session\SessionGuardServiceProvider::class);   
    }
}
```

## Declare the use in `config/auth.php`

```php
<?php

return [
    'guards' => [
        'guard_name' => [
            'driver' => 'easy.token', // <-- here is the declaration part
            'model' => \app\Models\User::class,
        ]
    ]
];
```

## Refresh authentication data

Add a method to the guard model, `\app\Models\User::class` in above example

This is useful when you have to refresh token after some specific action. Such as modified user's profile.

First you should mark the user's `id` as `TO-BE-UPDATED` when this kind of action performed,
then check if the authentication should be updated in the method `refreshAuth`, the `esay.token` will handle the rest for you

```php
<?php

class User
{
    /**
     * The user with given ID should be updated for the next visit
     * @param integer $id
     */
    public static function shouldRefreshAuth($id)
    {
        Cache::store()->set("should-update-auth-$id", 1, 120);
    }

    /**
     * Return to indicates if the Authentication should be refreshed, reload from database
     * - true to refresh auth and session
     * - false to keep it as it is
     *
     * @return boolean
     * 
     */
    public function refreshAuth()
    {
        $key = "should-update-auth-{$this->id}";
        if (Cache::store()->get($key) == 1) {
            Cache::store()->forget($key);
            return true;
        } else {
            return false;
        }
    }
}
```