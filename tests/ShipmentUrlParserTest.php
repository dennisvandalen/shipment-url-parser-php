<?php

use DennisVanDalen\ShipmentUrlParser\DTO\Shipment;
use DennisVanDalen\ShipmentUrlParser\ShipmentUrlParser;

it('can test', function () {
    expect(true)->toBeTrue();
});

it('can parse PostNL url', function () {
    $shipment = (new ShipmentUrlParser())
        ->parse('http://postnl.nl/tracktrace/?D=NL&B=TRACKING_CODE&P=ZIPCODE');

    expect($shipment)
        ->carrier->toBe(Shipment::POSTNL)
        ->trackingCode->toBe('TRACKING_CODE');
});

it('can parse DHL url', function () {
    $shipment = (new ShipmentUrlParser())
        ->parse('https://dhlparcel.nl/en/private/receiving/follow-your-shipment?tt=TRACKING_CODE&pc=ZIPCODE');

    expect($shipment)
        ->carrier->toBe(Shipment::DHL)
        ->trackingCode->toBe('TRACKING_CODE');
});

it('can parse my.DHL url', function () {
    $shipment = (new ShipmentUrlParser())
        ->parse('https://my.dhlparcel.nl/home/tracktrace/TRACKING_CODE/ZIPCODE?lang=nl_NL');

    expect($shipment)
        ->carrier->toBe(Shipment::DHL)
        ->trackingCode->toBe('TRACKING_CODE');
});

it('can parse UPS url', function () {
    $shipment = (new ShipmentUrlParser())
        ->parse('https://wwwapps.ups.com/tracking/tracking.cgi?tracknum=TRACKING_CODE');

    expect($shipment)
        ->carrier->toBe(Shipment::UPS)
        ->trackingCode->toBe('TRACKING_CODE');
});

it('can parse asendia url', function () {
    $shipment = (new ShipmentUrlParser())
        ->parse('https://tracking.asendia.com/tracking/TRACKING_CODE');

    expect($shipment)
        ->carrier->toBe(Shipment::ASENDIA)
        ->trackingCode->toBe('TRACKING_CODE');
});

it('can parse Onbezorgd url', function () {
    $shipment = (new ShipmentUrlParser())
        ->parse('https://pakket.onbezorgd.nl/trackandtrace.html?zipcode=ZIPCODE&orderreference=TRACKING_CODE');

    expect($shipment)
        ->carrier->toBe(Shipment::ONBEZORGD)
        ->trackingCode->toBe('TRACKING_CODE');

    $shipment = (new ShipmentUrlParser())
        ->parse('https://pakket.onbezorgd.nl/TrackAndTrace/tnt?Tracecode=TRACKING_CODE&Postalcode=ZIPCODE&Number=1');

    expect($shipment)
        ->carrier->toBe(Shipment::ONBEZORGD)
        ->trackingCode->toBe('TRACKING_CODE');


    $shipment = (new ShipmentUrlParser())
        ->parse('https://pakket.onbbezorgdienst.nl/trackandtrace.html?zipcode=1111AA&streetnumber=1&orderreference=TRACKING_CODE');

    expect($shipment)
        ->carrier->toBe(Shipment::ONBEZORGD)
        ->trackingCode->toBe('TRACKING_CODE');
});

it('can parse random url', function () {
    $shipment = (new ShipmentUrlParser())
        ->parse('https://fewjfbewjkfbkewjf.nl/trackandtrace.html?zipcode=ZIPCODE&orderreference=TRACKING_CODE');

    expect($shipment)
        ->toBeNull();
});
