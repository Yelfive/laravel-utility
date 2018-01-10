<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-09
 */

namespace fk\utility\Auth\Session;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class UserProvider implements \Illuminate\Contracts\Auth\UserProvider
{

    const IDENTITY_KEY = '__profile';

    protected $model;
    protected $checkExits = false;

    /**
     * UserProvider constructor.
     * @param array $config config('auth.<name>')
     */
    public function __construct($config)
    {
        $this->model = $config['model'];
        $this->checkExits = $config['checkExists'] ?? $this->checkExits;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * This is used to retrieve user data by TokenGuard::getName
     *
     * @see TokenGuard::getName
     *
     * @param  mixed $identifier
     * @return Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        // TODO: Implement retrieveById() method.
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed $identifier
     * @param  string $token
     * @return Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        if (false === Session::isStarted()) {
            Session::setId($identifier);
            Session::start();
        }

        if (!Session::get($token)) return null;

        $attributes = Session::get(self::IDENTITY_KEY);

        if (is_array($attributes) && isset($attributes['id'])) {
            /** @var \Illuminate\Database\Eloquent\Model|Authenticatable $user */
            if ($this->checkExits) {
                $user = new $this->model;
                $exists = $user->where(['id' => $attributes['id']])->count();
                if (!$exists) return null;
            }

            $user = new $this->model;
            $user->exists = true;
            $user->setRawAttributes($attributes)
                ->syncOriginal();
            if (method_exists($user, 'refreshAuth') && $user->refreshAuth()) {
                $user->refresh();
                Session::pull(self::IDENTITY_KEY, $user->getAttributes());
            }
            return $user;
        }
        return null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  Authenticatable $user
     * @param  string $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        // TODO: Implement updateRememberToken() method.
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        $query = $this->buildQuery();
        foreach ($credentials as $field => $value) {
            if ($field === 'password') continue;
            $query->where($field, $value);
        }
        return $query->first();
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  Authenticatable|\App\Models\User $user
     * @param  array $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        return Hash::check($credentials['password'], $user->password_hash);
    }

    protected function buildQuery(): Builder
    {
        return (new $this->model)->newQuery();
    }

}