@php
    $data = $this->getViewData();
    $pendingDeals    = $data['pendingDeals'];
    $preparingDeals  = $data['preparingDeals'];
    $inDeliveryDeals = $data['inDeliveryDeals'];
    $deliveredDeals  = $data['deliveredDeals'];
    $totalSales      = $data['totalSales'];
    $invoiceCount    = $data['invoiceCount'];
    $salesGrowth     = $data['salesGrowth'];
    $salesByDate     = $data['salesByDate'];
    $recentDeals     = $data['recentDeals'];
    $currentMonth    = $data['currentMonth'];

    $stageBadge = [
        'pending'                => ['label' => 'Pending',     'bg' => '#fff7ed', 'color' => '#c2410c', 'dot' => '#fb923c'],
        'preparing'              => ['label' => 'Preparing',   'bg' => '#fefce8', 'color' => '#a16207', 'dot' => '#facc15'],
        'handed_over_to_delivery'=> ['label' => 'In Delivery', 'bg' => '#ecfeff', 'color' => '#0e7490', 'dot' => '#22d3ee'],
        'delivered'              => ['label' => 'Delivered',   'bg' => '#f0fdf4', 'color' => '#15803d', 'dot' => '#4ade80'],
        'closed'                 => ['label' => 'Closed',      'bg' => '#f9fafb', 'color' => '#6b7280', 'dot' => '#9ca3af'],
    ];
@endphp

<x-filament-panels::page>

{{-- ── STAT CARDS ─────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:24px">

    {{-- Total Sales --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,.05)">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
            <div style="background:#ecfdf5;border-radius:10px;padding:10px;display:flex">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><line x1="12" y1="2" x2="12" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <span style="font-size:.82rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em">Sales — {{ $currentMonth }}</span>
        </div>
        <p style="font-size:1.8rem;font-weight:800;color:#111827;margin:0">Rs.&nbsp;{{ number_format($totalSales, 2) }}</p>
        <p style="margin:6px 0 0;font-size:.82rem;color:{{ $salesGrowth >= 0 ? '#16a34a' : '#dc2626' }}">
            {{ $salesGrowth >= 0 ? '▲' : '▼' }} {{ abs($salesGrowth) }}% vs last month
        </p>
    </div>

    {{-- Invoices --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,.05)">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
            <div style="background:#eff6ff;border-radius:10px;padding:10px;display:flex">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg>
            </div>
            <span style="font-size:.82rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em">Invoices this month</span>
        </div>
        <p style="font-size:1.8rem;font-weight:800;color:#111827;margin:0">{{ $invoiceCount }}</p>
        <p style="margin:6px 0 0;font-size:.82rem;color:#6b7280">Generated invoices</p>
    </div>

    {{-- Pending --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,.05)">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
            <div style="background:#fff7ed;border-radius:10px;padding:10px;display:flex">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <span style="font-size:.82rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em">Pending deals</span>
        </div>
        <p style="font-size:1.8rem;font-weight:800;color:#111827;margin:0">{{ $pendingDeals }}</p>
        <p style="margin:6px 0 0;font-size:.82rem;color:#6b7280">Awaiting action</p>
    </div>

    {{-- In Delivery --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,.05)">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
            <div style="background:#ecfeff;border-radius:10px;padding:10px;display:flex">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0891b2" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            </div>
            <span style="font-size:.82rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em">In delivery</span>
        </div>
        <p style="font-size:1.8rem;font-weight:800;color:#111827;margin:0">{{ $inDeliveryDeals }}</p>
        <p style="margin:6px 0 0;font-size:.82rem;color:#6b7280">Out for delivery</p>
    </div>

</div>

{{-- ── PIPELINE SUMMARY ────────────────────────────────────── --}}
<div style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:20px;box-shadow:0 1px 4px rgba(0,0,0,.05);margin-bottom:24px">
    <p style="margin:0 0 14px;font-size:.95rem;font-weight:700;color:#374151">Deal Pipeline</p>
    @php
        $pipeline = [
            ['label'=>'Pending',     'count'=>$pendingDeals,    'color'=>'#fb923c'],
            ['label'=>'Preparing',   'count'=>$preparingDeals,  'color'=>'#facc15'],
            ['label'=>'In Delivery', 'count'=>$inDeliveryDeals, 'color'=>'#22d3ee'],
            ['label'=>'Delivered',   'count'=>$deliveredDeals,  'color'=>'#4ade80'],
        ];
        $total = max(array_sum(array_column($pipeline, 'count')), 1);
    @endphp
    <div style="display:flex;border-radius:999px;overflow:hidden;height:10px;gap:2px;margin-bottom:14px">
        @foreach($pipeline as $seg)
            @if($seg['count'] > 0)
                <div style="background:{{ $seg['color'] }};flex:{{ $seg['count'] }};border-radius:999px"></div>
            @endif
        @endforeach
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:16px">
        @foreach($pipeline as $seg)
            <div style="display:flex;align-items:center;gap:6px;font-size:.85rem;color:#374151">
                <span style="width:10px;height:10px;border-radius:50%;background:{{ $seg['color'] }};display:inline-block"></span>
                <span style="font-weight:600">{{ $seg['label'] }}</span>
                <span style="color:#6b7280">({{ $seg['count'] }})</span>
            </div>
        @endforeach
    </div>
</div>

{{-- ── CHART + RECENT DEALS ────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start">

    {{-- Chart --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:22px;box-shadow:0 1px 4px rgba(0,0,0,.05)">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
            <p style="margin:0;font-size:.95rem;font-weight:700;color:#374151">Sales by Date</p>
            <span style="font-size:.8rem;font-weight:600;color:#6b7280;background:#f3f4f6;padding:4px 10px;border-radius:999px">{{ $currentMonth }}</span>
        </div>
        @if($salesByDate->isEmpty())
            <div style="height:240px;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#9ca3af">
                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
                <p style="margin:10px 0 0;font-size:.9rem">No sales data yet this month</p>
            </div>
        @else
            <canvas id="salesChart" style="max-height:280px"></canvas>
        @endif
    </div>

    {{-- Recent Deals --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:22px;box-shadow:0 1px 4px rgba(0,0,0,.05)">
        <p style="margin:0 0 16px;font-size:.95rem;font-weight:700;color:#374151">Recent Deals</p>
        @forelse($recentDeals as $deal)
            @php
                $stageKey = $deal->stage ?? 'pending';
                $badge = $stageBadge[$stageKey] ?? $stageBadge['pending'];
                $name = trim(($deal->contact?->first_name ?? '') . ' ' . ($deal->contact?->last_name ?? ''));
            @endphp
            <a href="{{ route('filament.app.resources.deals.edit', $deal->id) }}" style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;text-decoration:none;border-bottom:1px solid #f3f4f6">
                <div>
                    <p style="margin:0;font-size:.88rem;font-weight:600;color:#111827">#{{ $deal->id }} — {{ $name ?: 'No contact' }}</p>
                    <p style="margin:2px 0 0;font-size:.78rem;color:#9ca3af">{{ $deal->created_at->diffForHumans() }}</p>
                </div>
                <span style="font-size:.75rem;font-weight:700;padding:3px 10px;border-radius:999px;background:{{ $badge['bg'] }};color:{{ $badge['color'] }}">{{ $badge['label'] }}</span>
            </a>
        @empty
            <p style="font-size:.88rem;color:#9ca3af;margin:0">No deals yet.</p>
        @endforelse
    </div>

</div>

@if($salesByDate->isNotEmpty())
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [@foreach($salesByDate as $item)'{{ \Carbon\Carbon::parse($item['date'])->format('d M') }}',@endforeach],
            datasets: [
                {
                    label: 'Sales (Rs.)',
                    data: [@foreach($salesByDate as $item){{ round($item['total'], 2) }},@endforeach],
                    backgroundColor: 'rgba(14,165,233,0.18)',
                    borderColor: '#0ea5e9',
                    borderWidth: 2,
                    borderRadius: 6,
                    order: 2,
                },
                {
                    label: 'Invoices',
                    data: [@foreach($salesByDate as $item){{ $item['count'] }},@endforeach],
                    type: 'line',
                    borderColor: '#8b5cf6',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#8b5cf6',
                    yAxisID: 'y1',
                    order: 1,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'top', labels: { usePointStyle: true, padding: 16, font: { size: 12 } } },
                tooltip: {
                    padding: 10,
                    callbacks: {
                        label: ctx => ctx.dataset.yAxisID === 'y1'
                            ? ` ${ctx.parsed.y} invoice${ctx.parsed.y !== 1 ? 's' : ''}`
                            : ` Rs. ${Number(ctx.parsed.y).toLocaleString(undefined, { minimumFractionDigits: 2 })}`
                    }
                }
            },
            scales: {
                y:  { ticks: { callback: v => 'Rs. ' + v.toLocaleString() }, grid: { color: '#f3f4f6' } },
                y1: { position: 'right', grid: { drawOnChartArea: false }, ticks: { stepSize: 1 } },
                x:  { grid: { display: false } }
            }
        }
    });
});
</script>
@endif

</x-filament-panels::page>
