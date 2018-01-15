<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2018-01-15
 */

namespace App\Http\Middleware;

use App\Models\Admin;
use fk\helpers\privilege\AclManager;
use fk\utility\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AclAuthenticate extends \fk\utility\Auth\Middleware\AclAuthenticate
{
    public function authenticate(): bool
    {
        /** @var Admin $admin */
        $admin = Auth::user();
        if (is_numeric($admin->type) && $admin->type == Admin::TYPE_ROOT) return true;

        /** @var Request $request */
        $request = App::make('request');
        $uri = $request->route()->uri();
        $api = substr($uri, 8);

        return $this->acl()->authenticated(Session::get("privileges.$api"));
    }

    /**
     * @return AclManager
     */
    protected function acl()
    {
        return App::make(AclManager::class);
    }
}