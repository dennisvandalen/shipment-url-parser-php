<?php

namespace DennisVanDalen\ShipmentUrlParser\Redirects;

use DennisVanDalen\ShipmentUrlParser\ShipmentUrlParser;

class HttpRedirectResolver implements RedirectResolver
{
    public function resolve(string $url): array
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
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_TIMEOUT => 10,
            ]);
            curl_exec($curl);
            $redirectUrl = curl_getinfo($curl, CURLINFO_REDIRECT_URL);
            curl_close($curl);

            if ($redirectUrl) {
                $locations[] = $redirectUrl;
            }
        } catch (\Exception $ignored) {
        }

        return ShipmentUrlParser::flatten($locations);
    }
}
