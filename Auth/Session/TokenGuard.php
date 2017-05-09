<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-09
 */

namespace fk\utility\Auth\Session;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Session;

/**
 * @property Authenticatable $user
 * @property UserProvider $provider
 */
class TokenGuard extends SessionGuard implements Guard
{

    use GuardHelpers;

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if (!$this->user) {
            $identifier = $this->getToken();
            $this->user = $this->provider->retrieveByToken($identifier, '');
        }
        return $this->user;
    }

    protected function getToken()
    {
        return $_SERVER['HTTP_X_ACCESS_TOKEN'];
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
    }

    /**
     *
     * @param Authenticatable|\App\Models\User $user
     * @inheritdoc
     */
    public function setUser(Authenticatable $user)
    {
        parent::setUser($user);
        $provider = $this->provider;
        Session::put($provider::IDENTITY_KEY, $user->getAttributes());
        Session::regenerate(true);
    }

}