<?php

namespace App\Http\Controllers;

// DashboardController sekarang mendelegasikan ke TransactionController
// untuk menghindari duplikasi logika.
class DashboardController extends Controller
{
    public function index()
    {
        return app(TransactionController::class)->index(request());
    }
}
