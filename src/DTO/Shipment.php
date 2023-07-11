<?php

namespace DennisVanDalen\ShipmentUrlParser\DTO;

class Shipment
{
    public const DHL = 'DHL';
    public const ASENDIA = 'ASENDIA';
    public const POSTNL = 'POSTNL';
    public const ONBEZORGD = 'ONBEZORGD';
    public const UPS = 'UPS';

    public const UNKNOWN = 'UNKNOWN';

    public function __construct(
        public string $url,
        public string $trackingCode,
        public string $carrier,
        public string $carrierName,
    ) {
    }
}
