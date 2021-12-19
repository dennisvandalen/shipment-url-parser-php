<?php

namespace DennisVanDalen\ShipmentUrlParser;

use DennisVanDalen\ShipmentUrlParser\DTO\Shipment;

class ShipmentUrlParser
{
    public function parse(string $url, bool $handleRedirects = false)
    {
        if ($handleRedirects) {
            $urls = $this->resolveUrls($url);
        } else {
            $urls = [$url];
        }

        foreach ($urls as $url) {
            $trackingUrlComponents = parse_url($url);
            $host = $trackingUrlComponents['host'];

            try {
                if (str_contains($host, 'postnl')) {
                    return $this->postNlShipment($url, $trackingUrlComponents);
                } elseif (str_contains($host, 'pakket.onbezorgd.nl')) {
                    return $this->onbezorgdShipment($url, $trackingUrlComponents);
                } elseif (str_contains($host, 'dhlparcel.nl')) {
                    return $this->dhlShipment($url, $trackingUrlComponents);
                }
            } catch (\Exception $ignored) {
            }
        }
    }

    function resolveUrls($url)
    {
        $locations = [];
        $locations[] = $url;
        $getheaders = get_headers($url, true);

        ray($getheaders);

        if (isset($getheaders['location'])) {
            $locations[] = $getheaders['location'];
        }
        if (isset($getheaders['Location'])) {
            $locations[] = $getheaders['Location'];
        }

        return self::flatten($locations);
    }

    // Thanks Laravel
    public static function flatten(array $array, $depth = INF): array
    {
        $result = [];

        foreach ($array as $item) {
            if (!is_array($item)) {
                $result[] = $item;
            } else {
                $values = $depth === 1
                    ? array_values($item)
                    : static::flatten($item, $depth - 1);

                foreach ($values as $value) {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    private function postNlShipment(string $url, array $trackingUrlCompnents): Shipment
    {
        // http://postnl.nl/tracktrace/?D=NL&B=TRACKING_CODE&P=ZIPCODE
        parse_str($trackingUrlCompnents['query'], $params);
        $trackingCode = $params['B'];

        return new Shipment(
            url: $url,
            trackingNumber: $trackingCode,
            carrier: Shipment::POSTNL,
            carrierName: 'PostNL',
        );
    }

    private function onbezorgdShipment(string $url, array $trackingUrlCompnents)
    {
        // https://pakket.onbezorgd.nl/trackandtrace.html?zipcode=ZIPCODE&orderreference=TRACKING_CODE

        parse_str($trackingUrlCompnents['query'], $params);
        $trackingCode = $params['orderreference'];

        return new Shipment(
            url: $url,
            trackingNumber: $trackingCode,
            carrier: Shipment::ONBEZORGD,
            carrierName: 'Onbezorgd',
        );
    }

    private function dhlShipment(string $url, array $trackingUrlCompnents)
    {
        // https://dhlparcel.nl/en/private/receiving/follow-your-shipment?tt=TRACKING_CODE&pc=ZIPCODE

        parse_str($trackingUrlCompnents['query'], $params);
        $trackingCode = $params['tt'];

        return new Shipment(
            url: $url,
            trackingNumber: $trackingCode,
            carrier: Shipment::DHL,
            carrierName: 'DHL',
        );
    }
}
