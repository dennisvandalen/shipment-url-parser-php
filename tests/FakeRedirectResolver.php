<?php

namespace DennisVanDalen\ShipmentUrlParser\Tests;

use DennisVanDalen\ShipmentUrlParser\Redirects\RedirectResolver;

class FakeRedirectResolver implements RedirectResolver
{
    /** @var string[] */
    public array $resolved = [];

    /**
     * @param array<string, string> $redirects Map of URL => URL it redirects to.
     */
    public function __construct(private array $redirects = [])
    {
    }

    public function resolve(string $url): array
    {
        $this->resolved[] = $url;

        return isset($this->redirects[$url]) ? [$url, $this->redirects[$url]] : [$url];
    }
}
