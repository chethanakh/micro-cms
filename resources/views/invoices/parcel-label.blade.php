<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parcel Label {{ $invoice->invoice_number }}</title>
    @php
        $contact = $invoice->deal?->contact;
        $company = \App\Models\CompanyInformation::current();
        $companyName = $company->company_name ?: config('app.name');
        $companyPhones = collect([$company->phone_number, $company->mobile_number])
            ->filter(fn ($value) => filled($value))
            ->implode(' / ');
        $companyAddressLines = collect([
            $company->address_line_1,
            $company->address_line_2,
            trim(collect([$company->city, $company->postal_code])->filter()->implode(', ')),
        ])->filter(fn ($value) => filled($value));
        $companyLogoUrl = $company->logo_path
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($company->logo_path)
            : asset('images/temp-invoice-logo.svg');

        $customerName = trim(($contact?->first_name ?? '') . ' ' . ($contact?->last_name ?? ''));

        $toLines = collect([
            $customerName ?: null,
            $contact?->billing_address_line_1,
            $contact?->billing_address_line_2,
            trim(collect([$contact?->billing_city, $contact?->billing_postal_code])->filter()->implode(', ')),
            $contact?->phone_number,
        ])->filter(fn ($value) => filled($value));
    @endphp
    <style>
        :root {
            --paper-width: 210mm;
            --paper-height: 148mm;
            --bg: #ececec;
            --paper: #f4f4f4;
            --text: #111111;
            --line: #202020;
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            background: var(--bg);
            color: var(--text);
            font-family: "Avenir Next", "Segoe UI", Helvetica, Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            padding: 14px;
            display: grid;
            place-items: center;
        }

        .label {
            width: var(--paper-width);
            height: var(--paper-height);
            max-width: 100%;
            background: var(--paper);
            padding: 14mm 16mm 12mm;
            border: 1px solid #d7d7d7;
            display: grid;
            grid-template-rows: auto 1fr auto;
            row-gap: 6mm;
        }

        .logo {
            text-align: center;
        }

        .logo img {
            width: 42mm;
            height: 42mm;
            border-radius: 50%;
            object-fit: cover;
            display: inline-block;
        }

        .addresses {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14mm;
            align-items: start;
        }

        .block {
            font-size: 5.8mm;
            line-height: 1.28;
            font-weight: 700;
            color: var(--text);
        }

        .block .title {
            margin-bottom: 3mm;
        }

        .line {
            display: block;
            word-break: break-word;
        }

        .total-wrap {
            display: flex;
            justify-content: flex-end;
        }

        .total {
            border: 1px solid var(--line);
            padding: 4mm 6mm;
            font-size: 7.4mm;
            line-height: 1;
            font-weight: 800;
            background: transparent;
            min-width: 82mm;
            text-align: center;
        }

        @media print {
            @page {
                size: 210mm 148mm;
                margin: 0;
            }

            html,
            body {
                width: 210mm;
                height: 148mm;
                background: #ffffff !important;
                margin: 0;
                padding: 0;
            }

            .label {
                width: 210mm;
                height: 148mm;
                border: none;
                margin: 0;
                padding: 14mm 16mm 12mm;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>
    <div class="label">
        <div class="logo">
            <img src="{{ $companyLogoUrl }}" alt="{{ $companyName }} logo">
        </div>

        <section class="addresses">
            <div class="block">
                <div class="title">From -</div>
                <span class="line">{{ $companyName }}</span>
                @foreach ($companyAddressLines as $companyAddressLine)
                    <span class="line">{{ $companyAddressLine }}</span>
                @endforeach
                @if ($companyPhones)
                    <span class="line">{{ $companyPhones }}</span>
                @endif
            </div>

            <div class="block">
                <div class="title">To -</div>
                @forelse ($toLines as $line)
                    <span class="line">{{ $line }}</span>
                @empty
                    <span class="line">No destination address available</span>
                @endforelse
            </div>
        </section>

        <div class="total-wrap">
            <div class="total">Total - Rs.{{ number_format($grandTotal, 2) }}</div>
        </div>
    </div>

    @if (request()->get('print') === '1')
        <script>
            window.addEventListener('load', function () {
                window.print();
            });
        </script>
    @endif
</body>
</html>
