<?php

use DennisVanDalen\ShipmentUrlParser\DTO\Shipment;
use DennisVanDalen\ShipmentUrlParser\Tests\FakeRedirectResolver;

it('can test', function () {
    expect(true)->toBeTrue();
});

it('can parse PostNL url', function () {
    $shipment = parser()
        ->parse('http://postnl.nl/tracktrace/?D=NL&B=TRACKING_CODE&P=ZIPCODE');

    expect($shipment)
        ->carrier->toBe(Shipment::POSTNL)
        ->trackingCode->toBe('TRACKING_CODE');
});

it('can parse jouw PostNL url', function () {
    $shipment = parser()
        ->parse('https://jouw.postnl.nl/track-and-trace/TRACKING_CODE-NL-ZIPCODE');

    expect($shipment)
        ->carrier->toBe(Shipment::POSTNL)
        ->trackingCode->toBe('TRACKING_CODE');
});

it('can parse DHL url', function () {
    $shipment = parser()
        ->parse('https://dhlparcel.nl/en/private/receiving/follow-your-shipment?tt=TRACKING_CODE&pc=ZIPCODE');

    expect($shipment)
        ->carrier->toBe(Shipment::DHL)
        ->trackingCode->toBe('TRACKING_CODE');
});

it('can parse my.DHL url', function () {
    $shipment = parser()
        ->parse('https://my.dhlparcel.nl/home/tracktrace/TRACKING_CODE/ZIPCODE?lang=nl_NL');

    expect($shipment)
        ->carrier->toBe(Shipment::DHL)
        ->trackingCode->toBe('TRACKING_CODE');
});

it('can parse my.dhlecommerce url', function () {
    // anonymized tracking code and zipcode in the test URL
    $shipment = parser()
        ->parse('https://my.dhlecommerce.nl/home/tracktrace/TRACKING_CODE/ZIPCODE?lang=en-NL');

    expect($shipment)
        ->carrier->toBe(Shipment::DHL)
        ->trackingCode->toBe('TRACKING_CODE');
});

it('can parse DHL Express url', function () {
    $shipment = parser()
        ->parse('https://www.dhl.com/nl-en/home/tracking/tracking-express.html?tracking-id=TRACKING_CODE&submit=1');

    expect($shipment)
        ->carrier->toBe(Shipment::DHL)
        ->carrierName->toBe('DHL Express')
        ->trackingCode->toBe('TRACKING_CODE');
});

it('can parse UPS url', function () {
    $shipment = parser()
        ->parse('https://wwwapps.ups.com/tracking/tracking.cgi?tracknum=TRACKING_CODE');

    expect($shipment)
        ->carrier->toBe(Shipment::UPS)
        ->trackingCode->toBe('TRACKING_CODE');
});

it('can parse asendia url', function () {
    $shipment = parser()
        ->parse('https://tracking.asendia.com/tracking/TRACKING_CODE');

    expect($shipment)
        ->carrier->toBe(Shipment::ASENDIA)
        ->trackingCode->toBe('TRACKING_CODE');
});

it('can parse Onbezorgd url', function () {
    $shipment = parser()
        ->parse('https://pakket.onbezorgd.nl/trackandtrace.html?zipcode=ZIPCODE&orderreference=TRACKING_CODE');

    expect($shipment)
        ->carrier->toBe(Shipment::ONBEZORGD)
        ->trackingCode->toBe('TRACKING_CODE');

    $shipment = parser()
        ->parse('https://pakket.onbezorgd.nl/TrackAndTrace/tnt?Tracecode=TRACKING_CODE&Postalcode=ZIPCODE&Number=1');

    expect($shipment)
        ->carrier->toBe(Shipment::ONBEZORGD)
        ->trackingCode->toBe('TRACKING_CODE');


    $shipment = parser()
        ->parse('https://pakket.onbbezorgdienst.nl/trackandtrace.html?zipcode=ZIPCODE&streetnumber=1&orderreference=TRACKING_CODE');

    expect($shipment)
        ->carrier->toBe(Shipment::ONBEZORGD)
        ->trackingCode->toBe('TRACKING_CODE');
});

it('can parse random url', function () {
    $shipment = parser()
        ->parse('https://fewjfbewjkfbkewjf.nl/trackandtrace.html?zipcode=ZIPCODE&orderreference=TRACKING_CODE');

    expect($shipment)
        ->toBeNull();
});

it('can parse PostNL url with unescaped spaces', function () {
    $shipment = parser()
        ->parse('http://postnl.nl/tracktrace/?D=NL&B=TRACKING_CODE&P=7071 HD&T=C');

    expect($shipment)
        ->carrier->toBe(Shipment::POSTNL)
        ->trackingCode->toBe('TRACKING_CODE');
});

it('can parse jouw PostNL url with unescaped spaces', function () {
    $shipment = parser()
        ->parse('https://jouw.postnl.nl/track-and-trace/TRACKING_CODE-NL-7071 HD');

    expect($shipment)
        ->carrier->toBe(Shipment::POSTNL)
        ->trackingCode->toBe('TRACKING_CODE');
});

it('can parse Trans-Mission url', function () {
    $shipment = parser()
        ->parse('https://api.trans-mission.nl/tt/TRACKING_CODE/ZIPCODE');

    expect($shipment)
        ->carrier->toBe(Shipment::TRANSMISSION)
        ->carrierName->toBe('Trans-Mission')
        ->trackingCode->toBe('TRACKING_CODE');
});

it('follows a Wuunder redirect to Trans-Mission', function () {
    $shipment = parser([
        'https://api.wearewuunder.com/shipments/SHIPMENT_ID/track_and_trace?token=TOKEN' => 'https://api.trans-mission.nl/tt/TRACKING_CODE/ZIPCODE',
    ])->parse('https://api.wearewuunder.com/shipments/SHIPMENT_ID/track_and_trace?token=TOKEN');

    expect($shipment)
        ->url->toBe('https://api.trans-mission.nl/tt/TRACKING_CODE/ZIPCODE')
        ->carrier->toBe(Shipment::TRANSMISSION)
        ->trackingCode->toBe('TRACKING_CODE');
});

it('does not resolve redirects when disabled', function () {
    $resolver = new FakeRedirectResolver([
        'https://api.wearewuunder.com/shipments/SHIPMENT_ID/track_and_trace?token=TOKEN' => 'https://api.trans-mission.nl/tt/TRACKING_CODE/ZIPCODE',
    ]);

    $shipment = parser(resolver: $resolver)
        ->parse('https://api.wearewuunder.com/shipments/SHIPMENT_ID/track_and_trace?token=TOKEN', handleRedirects: false);

    expect($shipment)->toBeNull()
        ->and($resolver->resolved)->toBeEmpty();
});
