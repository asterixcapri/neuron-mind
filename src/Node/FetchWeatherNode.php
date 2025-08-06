<?php

namespace NeuronMind\Node;

use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\WorkflowState;

class FetchWeatherNode extends Node
{
    public function run(WorkflowState $state): WorkflowState
    {
        $city = $state->get('city');
        
        // Simulate a call to a weather API
        $temperature = rand(5, 25);
        $conditions = ['sunny', 'cloudy', 'rainy', 'windy'];
        $weather = $conditions[array_rand($conditions)];

        // The node just puts the raw data into the state.
        // It's not responsible for generating the final answer.
        $state->set('weather_data', [
            'city' => $city,
            'temperature' => $temperature,
            'conditions' => $weather,
        ]);

        return $state;
    }
}
