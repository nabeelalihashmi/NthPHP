<?php

namespace Cli;

abstract class BaseCommand {
    protected $name;
    protected $description;

    public function __construct(string $name, string $description) {
        $this->name = $name;
        $this->description = $description;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getDescription(): string {
        return $this->description;
    }

    abstract public function execute(array $options): void;
}
