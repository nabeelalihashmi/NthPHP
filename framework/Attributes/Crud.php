<?php

namespace Framework\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Crud {
    public array $enabledMethods;
    public string $path;
    public array $middlewares;

    public function __construct(array $enabledMethods = ['index', 'show', 'create', 'update', 'delete'], string $path = '', array $middlewares = []) {
        $this->enabledMethods = $enabledMethods;
        $this->path = $path;
        $this->middlewares = $middlewares;
    }
}
