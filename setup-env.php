<?php

declare(strict_types=1);

use Random\Engine\Secure;
use Random\Randomizer;

if (!file_exists('.env')) {
    $random = new Randomizer(new Secure());

    $reader = fopen('.env.example', 'rb');
    $writer = fopen('.env', 'wb');
    while (!feof($reader)) {
        $line = fgets($reader);
        if ($line === false) break;

        if (str_starts_with($line, 'APP_KEY=')) {
            $key = bin2hex($random->getBytes(32));
            $line = "APP_KEY=$key" . PHP_EOL;
        }
        fwrite($writer, $line);
    }
    fclose($reader);
    fclose($writer);

    echo 'Environment file created successfully.' . PHP_EOL;
} else {
    echo 'Environment file already exists.' . PHP_EOL;
}
