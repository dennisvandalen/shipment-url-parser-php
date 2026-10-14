<?php

namespace DennisVanDalen\ShipmentUrlParser\Redirects;

interface RedirectResolver
{
    /**
     * Returns the URL itself followed by any URLs it redirects to.
     *
     * @return string[]
     */
    public function resolve(string $url): array;
}
