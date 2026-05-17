<?php

namespace App\Http\Controllers;

use App\Enums\DealStage;
use App\Enums\DeliveryServiceProvider;
use App\Models\CompanyInformation;
use App\Models\Deal;
use App\Services\DeliveryTracking\DeliveryTrackingManager;
use Illuminate\View\View;

class PublicOrderTrackingController extends Controller
{
    public function __invoke(string $slug, DeliveryTrackingManager $trackingManager): View
    {
        $deal = Deal::query()
            ->with('contact')
            ->where('tracking_slug', $slug)
            ->firstOrFail();

        $company = CompanyInformation::current();
        $stage = DealStage::tryFrom((string) $deal->stage) ?? DealStage::Pending;
        $provider = $deal->delivery_service_provider instanceof DeliveryServiceProvider
            ? $deal->delivery_service_provider
            : DeliveryServiceProvider::tryFrom((string) $deal->delivery_service_provider);

        $trackingInfo = null;

        if ($stage === DealStage::HandedOverToDelivery && $provider && filled($deal->tracking_id)) {
            $trackingInfo = $trackingManager->track($provider, $deal->tracking_id);
        }

        return view('tracking.show', [
            'deal' => $deal,
            'company' => $company,
            'stage' => $stage,
            'provider' => $provider,
            'trackingInfo' => $trackingInfo,
            'reviewRequestUrl' => $company->review_request_url,
        ]);
    }
}
