<?php

namespace App\Interfaces;

interface RequestInterface
{
    public function path(): string;
    public function httpMethod(): string;
    public function isGet(): bool;
    public function isPost(): bool;
    public function isPut(): bool;
    public function isPatch(): bool;
    public function isDelete(): bool;
    public function isHead(): bool;
    public function isOptions(): bool;
    public function isMethod(string $method): bool;
    public function setInput($input): void;
    public function body(): array;
}
