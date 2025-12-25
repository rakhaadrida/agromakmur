<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Utilities\Constant;
use App\Utilities\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function indexSuperAdmin() {
        $startOfYear = Carbon::now()->startOfYear();
        $startOfMonth = Carbon::now()->startOfMonth();

        $totalAnnualSales = SalesOrder::query()
            ->where('status', '!=', 'CANCELLED')
            ->where('date', '>=', $startOfYear)
            ->sum('grand_total');

        $totalMonthlySales = SalesOrder::query()
            ->where('status', '!=', 'CANCELLED')
            ->where('date', '>=', $startOfMonth)
            ->sum('grand_total');

        $totalAnnualTransaction = SalesOrder::query()
            ->where('status', '!=', 'CANCELLED')
            ->where('date', '>=', $startOfYear)
            ->count();

        $totalMonthlyTransaction = SalesOrder::query()
            ->where('status', '!=', 'CANCELLED')
            ->where('date', '>=', $startOfMonth)
            ->count();

        $baseQueryReceivable = DashboardService::getBaseQueryTotalReceivable();
        $accountReceivable = $baseQueryReceivable
            ->where('sales_orders.date', '>=', $startOfYear)
            ->first();

        $totalAnnualReceivable = $accountReceivable->grand_total
            - $accountReceivable->payment_amount
            - $accountReceivable->return_amount;

        $salesPerMonth = SalesOrder::query()
            ->select(DB::raw('MONTH(date) AS month, SUM(grand_total) AS grand_total'))
            ->where('status', '!=', 'CANCELLED')
            ->where('date', '>=', $startOfYear)
            ->groupByRaw('MONTH(date)')
            ->orderBy('month')
            ->pluck('grand_total', 'month');

        $salesPerMonth = collect(range(1, 12))
            ->map(fn ($month) => (int) ($salesPerMonth[$month] ?? 0))
            ->values();

        $transactionPerType = SalesOrder::query()
            ->select(
                'type',
                DB::raw('COUNT(id) AS total_transaction')
            )
            ->where('status', '!=', 'CANCELLED')
            ->where('date', '>=', $startOfYear)
            ->groupBy('type')
            ->orderBy('type')
            ->pluck('total_transaction', 'type');

        $transactionPerType = collect(Constant::SALES_ORDER_TYPES)
            ->map(fn ($type) => (int) ($transactionPerType[$type] ?? 0))
            ->values();

        $data = [
            'totalAnnualSales' => $totalAnnualSales,
            'totalMonthlySales' => $totalMonthlySales,
            'totalAnnualTransaction' => $totalAnnualTransaction,
            'totalMonthlyTransaction' => $totalMonthlyTransaction,
            'totalAnnualReceivable' => $totalAnnualReceivable,
            'salesPerMonth' => $salesPerMonth,
            'transactionPerType' => $transactionPerType,
            'transactionTypes' => Constant::SALES_ORDER_TYPES
        ];

        return view('pages.admin.dashboard.super-admin', $data);
    }

    public function indexAdmin() {
        $startOfYear = Carbon::now()->startOfYear();
        $startOfMonth = Carbon::now()->startOfMonth();

        $totalAnnualTransaction = SalesOrder::query()
            ->where('status', '!=', 'CANCELLED')
            ->where('date', '>=', $startOfYear)
            ->count();

        $totalMonthlyTransaction = SalesOrder::query()
            ->where('status', '!=', 'CANCELLED')
            ->where('date', '>=', $startOfMonth)
            ->count();

        $totalUnprintedInvoice = SalesOrder::query()
            ->where('status', '!=', 'CANCELLED')
            ->where('is_printed', false)
            ->count();

        $totalPendingInvoice = SalesOrder::query()
            ->where('status',  Constant::SALES_ORDER_STATUS_WAITING_APPROVAL)
            ->count();

        $totalLowStockProduct = DashboardService::getBaseQueryLowStockProduct();

        $latestTransactions = SalesOrder::query()
            ->select('sales_orders.id', 'number', 'date', 'customers.name AS customer_name', 'grand_total')
            ->leftJoin('customers', 'customers.id', 'sales_orders.customer_id')
            ->latest('sales_orders.created_at')
            ->limit(6)
            ->get();

        $transactionPerStatus = SalesOrder::query()
            ->select(
                'status',
                DB::raw('COUNT(id) AS total_transaction')
            )
            ->where('date', '>=', $startOfYear)
            ->groupBy('status')
            ->orderBy('status')
            ->pluck('total_transaction', 'status');


        $transactionPerStatus = collect(Constant::SALES_ORDER_STATUSES)
            ->map(fn ($status) => (int) ($transactionPerStatus[$status] ?? 0))
            ->values();

        $data = [
            'totalAnnualTransaction' => $totalAnnualTransaction,
            'totalMonthlyTransaction' => $totalMonthlyTransaction,
            'totalUnprintedInvoice' => $totalUnprintedInvoice,
            'totalPendingInvoice' => $totalPendingInvoice,
            'totalLowStockProduct' => $totalLowStockProduct,
            'latestTransactions' => $latestTransactions,
            'transactionPerStatus' => $transactionPerStatus,
            'transactionStatuses' => Constant::SALES_ORDER_STATUSES
        ];

        return view('pages.admin.dashboard.admin', $data);
    }
}
