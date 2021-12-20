<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Laravel\Cashier\Cashier;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    protected $stripe;

    public function __construct()
    {
        /** @var Stripe\StripeClient $this->stripe */
        $this->stripe = Cashier::stripe();
    }
}
