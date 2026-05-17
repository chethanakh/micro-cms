<?php

namespace App\Filament\Pages;

use App\Models\Deal;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class Dashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Dashboard';

    protected string $view = 'filament.pages.dashboard';

    public function getViewData(): array
    {
        $currentMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();

        // Stage counts
        $stageCounts = Deal::query()
            ->select('stage', DB::raw('count(*) as total'))
            ->groupBy('stage')
            ->get()
            ->pluck('total', 'stage');

        $pendingDeals = $stageCounts->get('pending', 0);
        $preparingDeals = $stageCounts->get('preparing', 0);
        $inDeliveryDeals = $stageCounts->get('handed_over_to_delivery', 0);
        $deliveredDeals = $stageCounts->get('delivered', 0);

        // Sales helper: sum line items + delivery charges on a Collection of deals
        $calcSales = function ($deals): float {
            return (float) $deals->sum(function ($deal): float {
                $lineTotal = $deal->lineItems->sum(
                    fn ($item): float => (int) $item->quantity * (float) $item->unit_price
                );
                $delivery = $deal->delivery_charges_available
                    ? (float) $deal->delivery_charges
                    : 0.0;

                return $lineTotal + $delivery;
            });
        };

        // Load this month's deals with line items
        $thisMonthDeals = Deal::query()
            ->with('lineItems')
            ->where('created_at', '>=', $currentMonth)
            ->where('created_at', '<=', $endOfMonth)
            ->get();

        $totalSales = $calcSales($thisMonthDeals);
        $dealCount = $thisMonthDeals->count();

        // Last month for growth comparison
        $lastMonthDeals = Deal::query()
            ->with('lineItems')
            ->where('created_at', '>=', $lastMonth)
            ->where('created_at', '<=', $lastMonthEnd)
            ->get();

        $lastMonthSales = $calcSales($lastMonthDeals);

        $salesGrowth = $lastMonthSales > 0
            ? round((($totalSales - $lastMonthSales) / $lastMonthSales) * 100, 1)
            : ($totalSales > 0 ? 100 : 0);

        // Sales by date
        $salesByDate = $thisMonthDeals
            ->groupBy(fn ($deal): string => $deal->created_at->format('Y-m-d'))
            ->map(fn ($deals, string $date): array => [
                'date' => $date,
                'total' => $calcSales($deals),
                'count' => $deals->count(),
            ])
            ->sortKeys()
            ->values();

        // Recent deals (last 7)
        $recentDeals = Deal::query()
            ->with('contact')
            ->latest()
            ->limit(7)
            ->get();

        return [
            'pendingDeals' => $pendingDeals,
            'preparingDeals' => $preparingDeals,
            'inDeliveryDeals' => $inDeliveryDeals,
            'deliveredDeals' => $deliveredDeals,
            'totalSales' => $totalSales,
            'invoiceCount' => $dealCount,
            'salesGrowth' => $salesGrowth,
            'salesByDate' => $salesByDate,
            'recentDeals' => $recentDeals,
            'currentMonth' => now()->format('F Y'),
        ];
    }
}
