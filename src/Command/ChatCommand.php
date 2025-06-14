<?php

namespace NeuronMind\Command;

use NeuronAI\Chat\Messages\UserMessage;
use NeuronMind\Agent\ResearchAgent;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'chat')]
class ChatCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("<info>NeuronMind CLI - type 'exit' to quit</info>");
        $helper = new QuestionHelper();

        $researchAgent = ResearchAgent::make();

        while (true) {
            $question = new Question('You> ');
            $userInput = $helper->ask($input, $output, $question);

            if ($userInput === 'exit') {
                break;
            }

            $response = $researchAgent->chat(new UserMessage($userInput));
            $output->writeln("NeuronMind> ".$response->getContent());
        }

        return Command::SUCCESS;
    }
}
