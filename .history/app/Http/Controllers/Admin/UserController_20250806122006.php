<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Base\BaseController;
use App\Services\UserService;
use App\Models\User;
use App\Services\Validation\Rules\UserRules;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    public function __construct(UserService $userService)
    {
        parent::__construct(
            $userService,
            UserRules::class,
            'Admin/Users',
            'users',
            User::class
        );
    }

}
