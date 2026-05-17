<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Deal;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DealInvoiceStatusSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_changing_a_deal_stage_updates_the_latest_invoice_status(): void
    {
        $contact = Contact::create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'phone_number' => '1234567890',
            'mobile_number' => '1234567891',
            'whatsapp_number' => '1234567892',
            'billing_address_line_1' => '1 Billing Street',
            'billing_address_line_2' => null,
            'billing_city' => 'Colombo',
            'billing_postal_code' => '10000',
            'delivery_same_as_billing' => true,
            'delivery_address_line_1' => '1 Billing Street',
            'delivery_address_line_2' => null,
            'delivery_city' => 'Colombo',
            'delivery_postal_code' => '10000',
        ]);

        $deal = Deal::create([
            'contact_id' => $contact->id,
            'stage' => 'pending',
        ]);

        $firstInvoice = Invoice::create([
            'deal_id' => $deal->id,
            'invoice_number' => 'INV-00001',
            'status' => 'quote',
            'line_items' => [],
        ]);

        $latestInvoice = Invoice::create([
            'deal_id' => $deal->id,
            'invoice_number' => 'INV-00002',
            'status' => 'quote',
            'line_items' => [],
        ]);

        $deal->update([
            'stage' => 'closed',
        ]);

        $this->assertSame('quote', $firstInvoice->refresh()->status);
        $this->assertSame('paid', $latestInvoice->refresh()->status);
    }
}
