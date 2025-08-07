<?php

namespace NeuronMind\Node\Weather;

use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\WorkflowState;

class FetchWeatherNode extends Node
{
    public function run(WorkflowState $state): WorkflowState
    {
        if (!$state->has('city')) {
            // Should not happen if CheckCityNode is used, but as a safeguard
            return $state;
        }

        $city = $state->get('city');
        $date = $state->get('date', 'today'); // Default to 'today' if not provided
        
        // Simulate a call to a weather API
        $temperature = rand(5, 25);
        $conditions = ['sunny', 'cloudy', 'rainy', 'windy'];
        $weather = $conditions[array_rand($conditions)];

        $answer = sprintf(
            'The weather in %s for %s is %s with a temperature of %d°C.',
            $city,
            $date,
            $weather,
            $temperature
        );

        $state->set('answer', $answer);

        echo "DEBUG FetchWeatherNode state: "; print_r($state->all()); echo "\n";

        return $state;
    }
}
