<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalesTrendController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'daily');
        
        $chartData = $this->getChartData($period);
        
        return view('cashier.sales-trend.index', compact('chartData', 'period'));
    }

    private function getChartData($period)
    {
        $userId = Auth::id();
        
        switch ($period) {
            case 'daily':
                return $this->getDailyData($userId);
            case 'weekly':
                return $this->getWeeklyData($userId);
            case 'monthly':
                return $this->getMonthlyData($userId);
            case 'yearly':
                return $this->getYearlyData($userId);
            default:
                return $this->getDailyData($userId);
        }
    }

    private function getDailyData($userId)
    {
        $labels = [];
        $revenue = [];
        $transactions = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M d');
            
            // FIX: Exclude soft-deleted sales
            $daySales = Sale::whereNull('deleted_at')
                ->where('user_id', $userId)
                ->whereDate('sale_date', $date)
                ->get();
            
            $revenue[] = $daySales->sum('total_price');
            $transactions[] = $daySales->count();
        }
        
        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'transactions' => $transactions,
            'title' => 'Last 7 Days Sales',
            'subtitle' => 'Daily revenue and transactions'
        ];
    }

    private function getWeeklyData($userId)
    {
        $labels = [];
        $revenue = [];
        $transactions = [];
        
        for ($i = 3; $i >= 0; $i--) {
            $startOfWeek = Carbon::now()->subWeeks($i)->startOfWeek();
            $endOfWeek = Carbon::now()->subWeeks($i)->endOfWeek();
            $labels[] = $startOfWeek->format('M d') . ' - ' . $endOfWeek->format('M d');
            
            // FIX: Exclude soft-deleted sales
            $weekSales = Sale::whereNull('deleted_at')
                ->where('user_id', $userId)
                ->whereBetween('sale_date', [$startOfWeek, $endOfWeek])
                ->get();
            
            $revenue[] = $weekSales->sum('total_price');
            $transactions[] = $weekSales->count();
        }
        
        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'transactions' => $transactions,
            'title' => 'Last 4 Weeks Sales',
            'subtitle' => 'Weekly revenue and transactions'
        ];
    }

    private function getMonthlyData($userId)
    {
        $labels = [];
        $revenue = [];
        $transactions = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('F Y');
            
            // FIX: Exclude soft-deleted sales
            $monthSales = Sale::whereNull('deleted_at')
                ->where('user_id', $userId)
                ->whereYear('sale_date', $month->year)
                ->whereMonth('sale_date', $month->month)
                ->get();
            
            $revenue[] = $monthSales->sum('total_price');
            $transactions[] = $monthSales->count();
        }
        
        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'transactions' => $transactions,
            'title' => 'Last 6 Months Sales',
            'subtitle' => 'Monthly revenue and transactions'
        ];
    }

    private function getYearlyData($userId)
    {
        $labels = [];
        $revenue = [];
        $transactions = [];
        
        for ($i = 4; $i >= 0; $i--) {
            $year = Carbon::now()->subYears($i);
            $labels[] = $year->format('Y');
            
            // FIX: Exclude soft-deleted sales
            $yearSales = Sale::whereNull('deleted_at')
                ->where('user_id', $userId)
                ->whereYear('sale_date', $year->year)
                ->get();
            
            $revenue[] = $yearSales->sum('total_price');
            $transactions[] = $yearSales->count();
        }
        
        return [
            'labels' => $labels,
            'revenue' => $revenue,
            'transactions' => $transactions,
            'title' => 'Last 5 Years Sales',
            'subtitle' => 'Yearly revenue and transactions'
        ];
    }
}