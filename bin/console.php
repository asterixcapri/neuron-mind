#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use NeuronMind\Command\ChatCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/../.env');

$app = new Application();
$app->add(new ChatCommand());
$app->run();
