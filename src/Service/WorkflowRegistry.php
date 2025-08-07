<?php

namespace NeuronMind\Service;

use NeuronAI\Workflow\Workflow;

class WorkflowRegistry
{
    private array $workflows = [];

    public function register(string $name, string $description, Workflow $workflow, array $slots = []): void
    {
        $item = new \stdClass();
        $item->name = $name;
        $item->description = $description;
        $item->instance = $workflow;
        $item->slots = $slots;

        $this->workflows[] = $item;
    }

    public function get(string $name): ?\stdClass
    {
        foreach ($this->workflows as $workflow) {
            if ($workflow->name === $name) {
                return $workflow;
            }
        }

        return null;
    }

    public function getInstance(string $name): ?Workflow
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
