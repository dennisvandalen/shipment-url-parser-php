<?php

use DennisVanDalen\ShipmentUrlParser\ShipmentUrlParser;
use DennisVanDalen\ShipmentUrlParser\Tests\FakeRedirectResolver;

/**
 * Parser that never touches the network: redirects come from the given map.
 *
 * @param array<string, string> $redirects Map of URL => URL it redirects to.
 */
function parser(array $redirects = [], ?FakeRedirectResolver $resolver = null): ShipmentUrlParser
{
    return new ShipmentUrlParser($resolver ?? new FakeRedirectResolver($redirects));
}
