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
        ->trackingNumber->toBe('TRACKING_CODE');
});

it('can parse DHL url', function () {
    $shipment = (new ShipmentUrlParser())
        ->parse('https://dhlparcel.nl/en/private/receiving/follow-your-shipment?tt=TRACKING_CODE&pc=ZIPCODE');

    expect($shipment)
        ->carrier->toBe(Shipment::DHL)
        ->trackingNumber->toBe('TRACKING_CODE');
});

it('can parse Onbezorgd url', function () {
    $shipment = (new ShipmentUrlParser())
        ->parse('https://pakket.onbezorgd.nl/trackandtrace.html?zipcode=ZIPCODE&orderreference=TRACKING_CODE');

    expect($shipment)
        ->carrier->toBe(Shipment::ONBEZORGD)
        ->trackingNumber->toBe('TRACKING_CODE');
});
