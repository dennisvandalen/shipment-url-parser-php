<?php

namespace DennisVanDalen\ShipmentUrlParser\DTO;

class Shipment
{
    public const TNT = 'TNT';
    public const DHL = 'DHL';
    public const POSTNL = 'POSTNL';
    public const OTHER = 'OTHER';
    public const ONBEZORGD = 'ONBEZORGD';

    public function __construct(
        public string $url,
        public string $trackingNumber,
        public string $carrier,
        public string $carrierName,
    ) {
    }
}
