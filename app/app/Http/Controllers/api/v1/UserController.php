<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index() {

        return User::all();
    }
}
