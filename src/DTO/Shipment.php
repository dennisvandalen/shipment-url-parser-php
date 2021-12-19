<?php

namespace DennisVanDalen\ShipmentUrlParser\DTO;

class Shipment
{
    public const DHL = 'DHL';
    public const POSTNL = 'POSTNL';
    public const ONBEZORGD = 'ONBEZORGD';

    public function __construct(
        public string $url,
        public string $trackingCode,
        public string $carrier,
        public string $carrierName,
    ) {
    }
}
