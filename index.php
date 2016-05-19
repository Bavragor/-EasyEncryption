<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

use SecurityCompetition\Command\DecryptionCommand;
use SecurityCompetition\Command\EncryptionCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new EncryptionCommand());
$application->add(new DecryptionCommand());
$application->run();
