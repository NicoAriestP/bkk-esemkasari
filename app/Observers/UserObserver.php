<?php

namespace App\Observers;

use App\Traits\Observer\Blameable;

class UserObserver extends BaseObserver
{
    use Blameable;
}
