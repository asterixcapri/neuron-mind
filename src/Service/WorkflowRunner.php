<?php

namespace NeuronMind\Service;

use NeuronAI\Workflow\WorkflowInterrupt;
use NeuronAI\Workflow\WorkflowState;

class WorkflowRunner
{
    private ?array $interruptedState = null;

    public function __construct(
        private readonly WorkflowRegistry $workflowRegistry
    ) {}

    public function run(string $workflowName, array $initialData = []): array
    {
        $workflow = $this->workflowRegistry->getInstance($workflowName);

        if ($workflow === null) {
            throw new \InvalidArgumentException("Workflow '$workflowName' not found.");
        }

        $initialState = new WorkflowState($initialData);

        try {
            $finalState = $workflow->run($initialState);

            return [
                'status' => 'completed',
                'data' => $finalState->all()
            ];

        } catch (WorkflowInterrupt $e) {
            $interruptionData = $e->getData();

            $this->interruptedState = [
                'workflow_name' => $workflowName,
                'state' => $e->getState(),
                'details' => $interruptionData, // Store the whole directive
            ];

            return [
                'status' => 'interrupted',
                'details' => $interruptionData,
            ];
        }
    }

    public function resume(array $resumeData): array
    {
        if ($this->interruptedState === null) {
            return [
                'status' => 'error',
                'message' => 'No workflow to resume.'
            ];
        }

        $workflowName = $this->interruptedState['workflow_name'];
        $state = $this->interruptedState['state'];
        $workflow = $this->workflowRegistry->getInstance($workflowName);

        // Manually merge the resume data into the state before resuming.
        // This seems to be the runner's responsibility.
        foreach ($resumeData as $key => $value) {
            $state->set($key, $value);
        }

        try {
            // Now the state is complete when the workflow resumes.
            $finalState = $workflow->resume($resumeData, $state);
            $this->interruptedState = null; // Clean up
            
            return [
                'status' => 'completed',
                'data' => $finalState->all()
            ];
        } catch (WorkflowInterrupt $e) {
            $interruptionData = $e->getData();
            $this->interruptedState['state'] = $e->getState();
            $this->interruptedState['details'] = $interruptionData;

            return [
                'status' => 'interrupted',
                'details' => $interruptionData,
            ];
        }
    }

    public function getInterruptionDetails(): ?array
    {
        return $this->interruptedState['details'] ?? null;
    }

    public function isInterrupted(): bool
    {
        return $this->interruptedState !== null;
    }

    public function clearInterruptedState(): void
    {
        $this->interruptedState = null;
    }
}
