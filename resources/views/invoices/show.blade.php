<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    @php
        $contact = $invoice->deal?->contact;
        $fullName = trim(($contact?->first_name ?? '') . ' ' . ($contact?->last_name ?? ''));
        $statusKey = $invoice->status ?? 'quote';
        $statusLabel = \App\Models\Invoice::STATUS_OPTIONS[$statusKey] ?? ucfirst((string) $statusKey);
        $issueDate = $invoice->created_at;
        $dueDate = $issueDate?->copy()->addDays(30);
        $billingLines = collect([
            $fullName ?: null,
            $contact?->email,
            $contact?->phone_number,
            $contact?->billing_address_line_1,
            $contact?->billing_address_line_2,
            trim(collect([$contact?->billing_city, $contact?->billing_postal_code])->filter()->implode(' ')),
        ])->filter(fn ($value) => filled($value));
        $taxRate = 0;
        $taxAmount = $grandTotal * $taxRate;
        $totalDue = $grandTotal + $taxAmount;
    @endphp
    <style>
        :root {
            --paper-width: 560px;
            --paper-min-height: 794px;
            --page-bg: #eef0f3;
            --paper-bg: #fcfcfb;
            --paper-border: #cfd4da;
            --text: #3f3d3a;
            --muted: #85827d;
            --line: #c7c5c0;
            --accent: #2f2d2a;
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            background: var(--page-bg);
            color: var(--text);
            font-family: "Avenir Next", "Segoe UI", Helvetica, Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            padding: 28px 14px;
        }

        .paper {
            width: min(100%, var(--paper-width));
            min-height: var(--paper-min-height);
            margin: 0 auto;
            background: var(--paper-bg);
            border: 1px solid var(--paper-border);
            border-radius: 12px;
            box-shadow: 0 18px 45px rgba(24, 28, 33, 0.09);
            padding: 30px 32px 28px;
        }

        .top-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
            margin-bottom: 34px;
        }

        .invoice-title {
            font-size: 2.15rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            color: var(--accent);
            margin: 8px 0 0;
        }

        .logo-wrap {
            text-align: right;
        }

        .logo-wrap img {
            width: 124px;
            max-width: 100%;
            height: auto;
            display: inline-block;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 26px;
            margin-bottom: 26px;
        }

        .eyebrow {
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 10px;
        }

        .address-lines,
        .meta-lines,
        .payment-copy {
            font-size: 0.8rem;
            line-height: 1.55;
            color: var(--text);
        }

        .meta-lines {
            display: grid;
            grid-template-columns: auto 1fr;
            column-gap: 18px;
            row-gap: 6px;
            justify-content: end;
        }

        .meta-lines .label {
            color: var(--muted);
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            text-align: right;
        }

        .meta-lines .value {
            text-align: right;
            font-weight: 600;
            color: var(--accent);
        }

        .status-chip {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            margin-top: 14px;
            margin-left: auto;
            padding: 5px 12px;
            border: 1px solid var(--line);
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--accent);
            background: #f6f5f3;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        .items-table thead th {
            padding: 0 0 10px;
            border-bottom: 1px solid var(--line);
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: var(--muted);
            text-align: left;
        }

        .items-table thead th:nth-child(n + 2),
        .items-table tbody td:nth-child(n + 2) {
            text-align: right;
        }

        .items-table tbody td {
            padding: 8px 0;
            border-bottom: 1px solid rgba(199, 197, 192, 0.42);
            font-size: 0.8rem;
            color: var(--text);
            vertical-align: top;
        }

        .items-table tbody tr:last-child td {
            border-bottom: 1px solid var(--line);
        }

        .item-name {
            font-weight: 500;
            text-align: left !important;
        }

        .totals {
            width: 210px;
            margin-left: auto;
            margin-bottom: 28px;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding: 4px 0;
            font-size: 0.8rem;
            color: var(--text);
        }

        .totals-row .label {
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
            font-size: 0.68rem;
        }

        .totals-row.total {
            padding-top: 10px;
            margin-top: 10px;
            border-top: 1px solid var(--line);
            font-weight: 800;
        }

        .totals-row.total .label,
        .totals-row.total .value {
            color: var(--accent);
        }

        .bottom-row {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 32px;
            align-items: end;
        }

        .payment-copy {
            max-width: 320px;
        }

        .signature-block {
            text-align: right;
            min-width: 200px;
        }

        .signature-mark {
            font-family: "Brush Script MT", "Segoe Script", cursive;
            font-size: 2.6rem;
            line-height: 1;
            color: #4a4744;
            margin-bottom: 8px;
        }

        .signature-line {
            width: 180px;
            margin-left: auto;
            border-top: 1px solid var(--line);
            padding-top: 8px;
            font-size: 0.78rem;
            color: var(--muted);
        }

        .footer-note {
            margin-top: 22px;
            font-size: 0.64rem;
            color: var(--muted);
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        @media (max-width: 720px) {
            .paper {
                min-height: auto;
                padding: 24px 20px;
            }

            .top-row,
            .summary-grid,
            .bottom-row {
                grid-template-columns: 1fr;
                display: grid;
            }

            .logo-wrap,
            .meta-lines .label,
            .meta-lines .value,
            .signature-block {
                text-align: left;
            }

            .status-chip,
            .signature-line {
                margin-left: 0;
            }

            .invoice-title {
                font-size: 1.9rem;
            }

            .totals {
                width: 100%;
            }
        }

        @media print {
            @page {
                size: A5 portrait;
                margin: 0;
            }

            html, body {
                background: #ffffff !important;
            }

            body {
                padding: 0;
            }

            .paper {
                width: 148mm;
                min-height: 210mm;
                border: none;
                border-radius: 0;
                box-shadow: none;
                padding: 12mm 11mm 10mm;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>
    <div class="paper">
        <section class="top-row">
            <div>
                <h1 class="invoice-title">INVOICE</h1>
            </div>
            <div class="logo-wrap">
                <img src="{{ asset('images/temp-invoice-logo.svg') }}" alt="Temporary company logo">
            </div>
        </section>

        <section class="summary-grid">
            <div>
                <div class="eyebrow">Issued To</div>
                <div class="address-lines">
                    @forelse ($billingLines as $line)
                        <div>{{ $line }}</div>
                    @empty
                        <div>No billing contact available</div>
                    @endforelse
                </div>
            </div>

            <div>
                <div class="meta-lines">
                    <div class="label">Invoice No:</div>
                    <div class="value">{{ $invoice->invoice_number }}</div>

                    <div class="label">Date:</div>
                    <div class="value">{{ $issueDate?->format('d.m.Y') }}</div>

                    <div class="label">Due Date:</div>
                    <div class="value">{{ $dueDate?->format('d.m.Y') }}</div>

                    <div class="label">Deal Ref:</div>
                    <div class="value">{{ str_pad((string) $invoice->deal_id, 5, '0', STR_PAD_LEFT) }}</div>
                </div>
                <div class="status-chip">{{ $statusLabel }}</div>
            </div>
        </section>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Rate</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($lineItems as $item)
                    @php
                        $unitPrice = (float) ($item['unit_price'] ?? 0);
                        $quantity = (float) ($item['quantity'] ?? 0);
                        $lineTotal = $unitPrice * $quantity;
                    @endphp
                    <tr>
                        <td class="item-name">{{ $item['product_name'] ?? 'Unnamed Product' }}</td>
                        <td>{{ number_format($unitPrice, 2) }}</td>
                        <td>{{ rtrim(rtrim(number_format($quantity, 2, '.', ''), '0'), '.') }}</td>
                        <td>{{ number_format($lineTotal, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="item-name">No line items recorded</td>
                        <td>0.00</td>
                        <td>0</td>
                        <td>0.00</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <section class="totals">
            <div class="totals-row">
                <span class="label">Subtotal</span>
                <span class="value">{{ number_format($grandTotal, 2) }}</span>
            </div>
            <div class="totals-row">
                <span class="label">Tax</span>
                <span class="value">{{ $taxRate * 100 }}%</span>
            </div>
            <div class="totals-row total">
                <span class="label">Total</span>
                <span class="value">{{ number_format($totalDue, 2) }}</span>
            </div>
        </section>


        <div class="footer-note">
            Generated {{ now()->format('d.m.Y') }} • Thank you for trusting us.
        </div>
    </div>

    @if(request()->get('print') === '1')
        <script>
            window.addEventListener('load', function () {
                window.print();
            });
        </script>
    @endif
</body>
</html>
