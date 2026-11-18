<?php

namespace App\Controllers\Customer;

use Core\Controller;

class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('customer/home', ['title' => 'Trang chủ MotoShop']);
    }
}
