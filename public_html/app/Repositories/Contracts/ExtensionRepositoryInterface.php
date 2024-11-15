<?php

namespace App\Repositories\Contracts;

use Closure;

interface ExtensionRepositoryInterface extends PortalRepositoryInterface
{
    public function licensed(array $data);

    public function extensions();

    public function themes();

    public function subscription();

    public function all(bool $isTheme = false);

    public function find(string $slug);

    public function findId(int $id);

    public function install(string $slug, string $version);

    public function request(string $method, string $route, array $body = []);

    public function check($request, Closure $next);

    public function appVersion(): bool|string|int;

    public function cart(): ?array;
}
