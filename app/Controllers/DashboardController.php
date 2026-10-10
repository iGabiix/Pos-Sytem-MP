<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CustomerModel;
use App\Models\SaleModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $today = date('Y-m-d');
        $todayStats = $db->table('sales')->select('COUNT(*) AS transactions, COALESCE(SUM(total_price), 0) AS revenue', false)
            ->where('created_at >=', $today . ' 00:00:00')->where('created_at <', date('Y-m-d', strtotime('+1 day')) . ' 00:00:00')->get()->getRowArray();
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime('-' . $i . ' days'));
            $days[$date] = ['label' => date('D', strtotime($date)), 'total' => 0];
        }
        $weekly = $db->table('sales')->select('created_at, total_price')->where('created_at >=', array_key_first($days) . ' 00:00:00')->get()->getResultArray();
        foreach ($weekly as $sale) {
            $date = substr($sale['created_at'], 0, 10);
            if (isset($days[$date])) {
                $days[$date]['total'] += (float) $sale['total_price'];
            }
        }
        return $this->page('dashboard', [
            'title' => 'Overview', 'today' => $todayStats, 'days' => $days,
            'productsCount' => (new ProductModel())->countAllResults(),
            'customersCount' => (new CustomerModel())->countAllResults(),
            'lowStockCount' => (new ProductModel())->where('stock_quantity <=', 10)->countAllResults(),
            'lowStock' => (new ProductModel())->where('stock_quantity <=', 10)->orderBy('stock_quantity')->findAll(4),
            'recent' => (new SaleModel())->history()->findAll(5),
        ]);
    }
}

