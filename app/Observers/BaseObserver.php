<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;

class BaseObserver
{
    public function __construct()
    {
        if (App::runningInConsole()) {
            Auth::loginUsingId(1);
        }
    }
}
