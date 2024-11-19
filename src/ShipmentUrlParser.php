<?php

namespace DennisVanDalen\ShipmentUrlParser;

use DennisVanDalen\ShipmentUrlParser\DTO\Shipment;

class ShipmentUrlParser
{
    public function parse(string $url, bool $handleRedirects = true): ?Shipment
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
                if (str_contains($host, 'jouw.postnl.nl')) {
                    return $this->jouwPostNlShipment($url, $trackingUrlComponents);
                } elseif (str_contains($host, 'postnl')) {
                    return $this->postNlShipment($url, $trackingUrlComponents);
                } elseif (str_contains($host, 'pakket.onbezorgd.nl')) {
                    return $this->onbezorgdShipment($url, $trackingUrlComponents);
                } elseif (str_contains($host, 'pakket.onbbezorgdienst.nl')) {
                    return $this->onbezorgdShipment($url, $trackingUrlComponents);
                } elseif (str_contains($host, 'my.dhlparcel.nl')) {
                    return $this->myDhlShipment($url, $trackingUrlComponents);
                } elseif (str_contains($host, 'dhlparcel.nl')) {
                    return $this->dhlShipment($url, $trackingUrlComponents);
                } elseif (str_contains($host, 'asendia.com')) {
                    return $this->asendiaShipment($url, $trackingUrlComponents);
                } elseif (str_contains($host, 'ups.com')) {
                    return $this->upsShipment($url, $trackingUrlComponents);
                }
            } catch (\Exception $ignored) {
            }
        }

        return null;
    }

    public function resolveUrls($url): array
    {
        ini_set('default_socket_timeout', 10);

        $locations = [];
        $locations[] = $url;

        try {
            $getHeaders = get_headers($url, true);

            if (isset($getHeaders['location'])) {
                $locations[] = $getHeaders['location'];
            }
            if (isset($getHeaders['Location'])) {
                $locations[] = $getHeaders['Location'];
            }
        } catch (\Exception $ignored) {
        }

        // try curl redirect as well
        try {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $url,
            ]);
            curl_exec($curl);
            $redirectUrl = curl_getinfo($curl, CURLINFO_REDIRECT_URL);
            curl_close($curl);

            if ($redirectUrl) {
                $locations[] = $redirectUrl;
            }
        } catch (\Exception $ignored) {
        }

        return self::flatten($locations);
    }

    // Thanks Laravel
    public static function flatten(array $array, $depth = INF): array
    {
        $result = [];

        foreach ($array as $item) {
            if (! is_array($item)) {
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

    private function jouwPostNlShipment(string $url, array $trackingUrlCompnents): Shipment
    {
        // regex https://jouw.postnl.nl/track-and-trace/TRACKING_CODE-NL-ZIPCODE
        if (preg_match('~track-and-trace/([^-]+)-~', $url, $matches)) {
            $trackingCode = $matches[1];
        }

        return new Shipment(
            url: $url,
            trackingCode: $trackingCode,
            carrier: Shipment::POSTNL,
            carrierName: 'PostNL',
        );
    }

    private function postNlShipment(string $url, array $trackingUrlCompnents): Shipment
    {
        // http://postnl.nl/tracktrace/?D=NL&B=TRACKING_CODE&P=ZIPCODE
        parse_str($trackingUrlCompnents['query'], $params);
        $trackingCode = $params['B'];

        return new Shipment(
            url: $url,
            trackingCode: $trackingCode,
            carrier: Shipment::POSTNL,
            carrierName: 'PostNL',
        );
    }

    private function upsShipment(string $url, array $trackingUrlCompnents): Shipment
    {
        // https://wwwapps.ups.com/tracking/tracking.cgi?tracknum=TRACKING_CODE
        parse_str($trackingUrlCompnents['query'], $params);
        $trackingCode = $params['tracknum'];

        return new Shipment(
            url: $url,
            trackingCode: $trackingCode,
            carrier: Shipment::UPS,
            carrierName: 'UPS',
        );
    }

    private function onbezorgdShipment(string $url, array $trackingUrlCompnents): Shipment
    {
        // https://pakket.onbezorgd.nl/trackandtrace.html?zipcode=ZIPCODE&orderreference=TRACKING_CODE

        parse_str($trackingUrlCompnents['query'], $params);
        $trackingCode = '';

        try {
            $trackingCode = $params['orderreference'];
        } catch (\Exception $e) {
        }

        try {
            $trackingCode = $params['Tracecode'];
        } catch (\Exception $e) {
        }

        return new Shipment(
            url: $url,
            trackingCode: $trackingCode,
            carrier: Shipment::ONBEZORGD,
            carrierName: 'Onbezorgd',
        );
    }

    private function dhlShipment(string $url, array $trackingUrlCompnents): Shipment
    {
        // https://dhlparcel.nl/en/private/receiving/follow-your-shipment?tt=TRACKING_CODE&pc=ZIPCODE
        parse_str($trackingUrlCompnents['query'], $params);
        $trackingCode = $params['tt'];

        return new Shipment(
            url: $url,
            trackingCode: $trackingCode,
            carrier: Shipment::DHL,
            carrierName: 'DHL',
        );
    }

    private function asendiaShipment(string $url, array $trackingUrlCompnents): Shipment
    {
        // https://tracking.asendia.com/tracking/LF000000000FR
        return new Shipment(
            url: $url,
            trackingCode: basename($trackingUrlCompnents['path']),
            carrier: Shipment::ASENDIA,
            carrierName: 'Asendia',
        );
    }

    private function myDhlShipment(mixed $url, ?array $trackingUrlComponents): Shipment
    {
        // https://my.dhlparcel.nl/home/tracktrace/TRACKING_CODE/ZIPCODE?lang=nl_NL
        // Split url by /
        $urlParts = explode('/', $trackingUrlComponents['path']);
        $trackingCode = '';

        // Iterate over the parts
        for ($i = 0; $i < count($urlParts); $i++) {
            // If the part is 'tracktrace', take the next part as the tracking code
            if ($urlParts[$i] === 'tracktrace' && isset($urlParts[$i + 1])) {
                $trackingCode = $urlParts[$i + 1];

                break;
            }
        }

        return new Shipment(
            url: $url,
            trackingCode: $trackingCode,
            carrier: Shipment::DHL,
            carrierName: 'DHL',
        );
    }
}
