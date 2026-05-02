<?php

namespace Modules\Auth\Http\Controllers\Admin;

use Illuminate\View\View;
use Modules\Auth\Services\DashboardService;

class DashboardController extends AdminController
{
    public function __construct(
        private readonly DashboardService $service
    ) {
        parent::__construct();
    }

    public function index(): View
    {
        $kpi = $this->service->getKpiData();
        $orders = $this->service->getOrdersOverview();
        $categories = $this->service->getTopCategories(3);
        $productsStats = $this->service->getProductsStats();

        return view('admin.dashboard', compact('kpi', 'orders', 'categories', 'productsStats'));
    }
}
