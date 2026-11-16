<?php

declare(strict_types=1);

const COMPOSER_URL = 'https://getcomposer.org/download/2.11.2/composer.phar';
const COMPOSER_HASH = 'dd158c0afd41d15fac89e1b4e47b5d42911fe316c3687cd5edac98319b25d028';

function download(): mixed
{
    if (!extension_loaded('curl')) {
        throw new RuntimeException('cURL extension is required');
    }

    $output = tmpfile();
    if ($output === false) {
        throw new RuntimeException('Failed to create temporary file');
    }

    $ch = curl_init(COMPOSER_URL);
    if (!curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FILE => $output,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_PROGRESSFUNCTION => static function ($resource, $downloadSize, $downloaded) {
            fwrite(STDOUT, "Downloaded: $downloaded/$downloadSize bytes\r");
            return false;
        },
        CURLOPT_NOPROGRESS => false,
    ])) {
        curl_close($ch);
        throw new RuntimeException('Failed to set cURL options');
    }

    if (!curl_exec($ch)) {
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        throw new RuntimeException('Failed to download composer.phar. HTTP code: ' . $httpCode);
    }

    curl_close($ch);

    $outputMeta = stream_get_meta_data($output);
    $isSeekable = (bool)$outputMeta['seekable'];
    if ($isSeekable) {
        rewind($output);
        $contents = stream_get_contents($output);
        $hash = hash('sha256', $contents);
        if ($hash !== COMPOSER_HASH) throw new RuntimeException('Invalid composer.phar hash');
        rewind($output);
    } else {
        trigger_error('Destination stream is not seekable. This may cause issues with the downloaded file.', E_USER_WARNING);
    }

    return $output;
}

$start = microtime(true);
stream_copy_to_stream(download(), fopen('composer.phar', 'w'));
$end = microtime(true);

// Test the phar
$phar = new Phar('composer.phar');
if (!$phar->valid()) {
    unlink('composer.phar');
    throw new RuntimeException('Invalid phar');
}

echo 'Downloaded composer.phar in ' . ($end - $start) . ' seconds', PHP_EOL;
echo 'Use `php composer.phar` to run Composer commands', PHP_EOL;
