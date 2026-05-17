<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order {{ $deal->id }}</title>
    <style>
        :root {
            --bg: #f3f4f6;
            --card: #ffffff;
            --ink: #0f172a;
            --muted: #475569;
            --line: #e2e8f0;
            --accent: #0ea5e9;
            --success: #16a34a;
            --warn: #d97706;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Avenir Next", "Segoe UI", Helvetica, Arial, sans-serif;
            background:
                radial-gradient(circle at 12% 8%, #dbeafe 0%, transparent 35%),
                radial-gradient(circle at 85% 85%, #cffafe 0%, transparent 42%),
                var(--bg);
            color: var(--ink);
            padding: 24px 14px 42px;
        }

        .shell {
            width: min(940px, 100%);
            margin: 0 auto;
            display: grid;
            gap: 16px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 20px;
            box-shadow: 0 20px 46px rgba(15, 23, 42, 0.08);
            padding: 22px;
        }

        .hero {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 18px;
            align-items: center;
        }

        .hero-logo {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #dbeafe;
        }

        .title {
            margin: 0;
            font-size: clamp(1.2rem, 2.8vw, 1.8rem);
            letter-spacing: 0.03em;
        }

        .subtitle {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 0.95rem;
        }

        .status-pill {
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 7px 12px;
            font-weight: 700;
            font-size: 0.82rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            border: 1px solid transparent;
        }

        .status-pill.preparing {
            background: #fff7ed;
            color: var(--warn);
            border-color: #fed7aa;
        }

        .status-pill.transit {
            background: #ecfeff;
            color: #0e7490;
            border-color: #99f6e4;
        }

        .status-pill.done {
            background: #ecfdf5;
            color: var(--success);
            border-color: #86efac;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .meta {
            display: grid;
            gap: 6px;
            font-size: 0.95rem;
            color: var(--muted);
        }

        .meta strong {
            color: var(--ink);
        }

        .provider {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 12px;
            align-items: center;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px dashed var(--line);
        }

        .provider img {
            width: 130px;
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            border: 1px solid var(--line);
            background: #ffffff;
            padding: 5px;
        }

        .tracking-data {
            margin-top: 10px;
            padding: 14px;
            border-radius: 14px;
            border: 1px solid var(--line);
            background: #f8fafc;
            display: grid;
            gap: 8px;
        }

        .tracking-data h3 {
            margin: 0;
            font-size: 1rem;
        }

        .tracking-row {
            font-size: 0.95rem;
            color: var(--muted);
        }

        .tracking-row b {
            color: var(--ink);
        }

        .review-btn {
            margin-top: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 11px 16px;
            border-radius: 12px;
            border: 1px solid #0ea5e9;
            color: #075985;
            background: #ecfeff;
            font-weight: 700;
            text-decoration: none;
            transition: transform 140ms ease, box-shadow 140ms ease;
        }

        .review-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(14, 165, 233, 0.24);
        }

        .share-section {
            margin-top: 20px;
            padding: 16px;
            border-radius: 14px;
            border: 1px solid var(--line);
            background: #f8fafc;
        }

        .share-section h3 {
            margin-top: 0;
            color: var(--ink);
            font-size: 1rem;
        }

        .tracking-url-display {
            background: var(--card);
            border: 1px solid var(--line);
            padding: 12px;
            border-radius: 10px;
            margin: 12px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .tracking-url-display a {
            flex: 1;
            min-width: 250px;
            color: var(--accent);
            text-decoration: none;
            word-break: break-all;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .tracking-url-display a:hover {
            text-decoration: underline;
        }

        .share-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin: 12px 0;
        }

        .share-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid transparent;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: none;
            transition: transform 140ms ease, box-shadow 140ms ease;
        }

        .share-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.12);
        }

        .share-btn.whatsapp {
            background: #25d366;
            color: white;
            border-color: #20ba5a;
        }

        .share-btn.copy {
            background: var(--accent);
            color: white;
            border-color: #0284c7;
        }

        .share-btn.copy:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .message-template {
            margin-top: 12px;
            padding: 14px;
            border-radius: 10px;
            background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%);
            border: 1px solid #bae6fd;
            font-size: 0.9rem;
            line-height: 1.6;
            color: var(--ink);
            white-space: pre-wrap;
            word-break: break-word;
        }

        @media (max-width: 760px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .provider {
                grid-template-columns: 1fr;
            }

            .share-buttons {
                flex-direction: column;
            }

            .share-btn {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    @php
        $stageValue = $stage->value;
        $isPreparing = in_array($stageValue, ['pending', 'preparing'], true);
        $isTransit = $stageValue === 'handed_over_to_delivery';
        $isDone = in_array($stageValue, ['delivered', 'closed'], true);
        $customerName = trim(($deal->contact?->first_name ?? '').' '.($deal->contact?->last_name ?? ''));
    @endphp

    <main class="shell">
        <section class="card hero">
            <img class="hero-logo" src="{{ $company->logo_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($company->logo_path) : asset('images/temp-invoice-logo.svg') }}" alt="{{ $company->company_name }} logo">
            <div>
                <h1 class="title">Order Tracking</h1>
                <p class="subtitle">Track your parcel status in real-time.</p>
                <span class="status-pill {{ $isPreparing ? 'preparing' : ($isTransit ? 'transit' : 'done') }}">
                    {{ $stage->label() }}
                </span>
            </div>
        </section>

        <section class="card grid">
            <div>
                <h2 style="margin-top:0">Order Details</h2>
                <div class="meta">
                    <div><strong>Order ID:</strong> #{{ $deal->id }}</div>
                    <div><strong>Customer:</strong> {{ $customerName ?: 'N/A' }}</div>
                    <div><strong>Tracking ID:</strong> {{ $deal->tracking_id ?: 'Not assigned yet' }}</div>
                    <div><strong>Last Updated:</strong> {{ optional($deal->updated_at)->format('Y-m-d H:i:s') }}</div>
                </div>

                @if ($provider)
                    <div class="provider">
                        <img src="{{ $provider->logoPath() }}" alt="{{ $provider->label() }} logo">
                        <div class="meta">
                            <div><strong>Delivery Service:</strong> {{ $provider->label() }}</div>
                        </div>
                    </div>
                @endif
            </div>

            <div>
                <h2 style="margin-top:0">Status</h2>

                @if ($isPreparing)
                    <div class="tracking-data">
                        <h3>Order Preparing</h3>
                        <div class="tracking-row">Your order is being prepared. Tracking link will be available once handed over to delivery.</div>
                    </div>
                @elseif ($isTransit)
                    <div class="tracking-data">
                        <h3>Live Delivery Information</h3>
                        @if ($trackingInfo)
                            <div class="tracking-row"><b>Tracking ID:</b> {{ $trackingInfo->trackingId }}</div>
                            <div class="tracking-row"><b>Status:</b> {{ $trackingInfo->status }}</div>
                            <div class="tracking-row"><b>Location:</b> {{ $trackingInfo->location ?? 'N/A' }}</div>
                            <div class="tracking-row"><b>Last Update:</b> {{ $trackingInfo->lastUpdate ?? 'N/A' }}</div>
                        @else
                            <div class="tracking-row">Delivery has started. Live carrier information is currently unavailable.</div>
                        @endif
                    </div>
                @else
                    <div class="tracking-data">
                        <h3>Delivered</h3>
                        <div class="tracking-row">Your order has been delivered successfully.</div>
                    </div>

                    @if (filled($reviewRequestUrl))
                        <a class="review-btn" href="{{ $reviewRequestUrl }}" target="_blank" rel="noopener">Leave a Review</a>
                    @endif
                @endif
            </div>
        </section>
    </main>
</body>
</html>
