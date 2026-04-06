<?php

namespace Modules\Auth\Http\Controllers\Admin;

use Illuminate\View\View;

class DashboardController extends AdminController
{
    public function index(): View
    {
        return view('admin.dashboard');
    }
}
