<?php

namespace Modules\Auth\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

abstract class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:admin.access');
    }
}
