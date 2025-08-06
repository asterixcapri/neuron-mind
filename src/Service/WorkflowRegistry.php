<?php

namespace NeuronMind\Service;

use NeuronAI\Workflow\Workflow;

class WorkflowRegistry
{
    private array $workflows = [];

    public function register(string $name, string $description, Workflow $workflow): void
    {
        $item = new \stdClass();
        $item->name = $name;
        $item->description = $description;
        $item->instance = $workflow;

        $this->workflows[] = $item;
    }

    public function get(string $name): ?Workflow
    {
        foreach ($this->workflows as $workflow) {
            if ($workflow->name === $name) {
                return $workflow->instance;
            }
        }

        return null;
    }

    public function all(): array
    {
        return $this->workflows;
    }
}
