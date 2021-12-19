<?php

namespace DennisVanDalen\ShipmentUrlParser\DTO;

class Shipment
{
    const TNT = 'TNT';
    const DHL = 'DHL';
    const POSTNL = 'POSTNL';
    const OTHER = 'OTHER';
    const ONBEZORGD = 'ONBEZORGD';

    public function __construct(
        public string $url,
        public string $trackingNumber,
        public string $carrier,
        public string $carrierName,
    )
    {
    }
}
