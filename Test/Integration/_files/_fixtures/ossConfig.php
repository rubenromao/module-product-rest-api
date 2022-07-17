<?php

use Rezolve\APISalesV4\Model\OSS\Config;

$paramFile = __DIR__ . '/../../../../../../../shellscripts/installParameters.bash';

if (!file_exists($paramFile)) {
    throw new \Exception("install param file does not exist");
}

$fileHandle = fopen($paramFile, 'r');

$ossValues = [];

while ($line = fgets($fileHandle)) {
    if (strpos($line, 'oss') !== 0) {
        continue;
    }

    preg_match("#^([^=]*)='([^']*)'$#", $line, $matches);

    $ossValues[$matches[1]] = $matches[2];
}

$ossConfigArray = [
    ['path' => Config::KEY_ID_PATH, 'value' => $ossValues['ossKeyId'], 'encrypt' => true],
    ['path' => Config::KEY_SECRET_PATH, 'value' => $ossValues['ossKeySecret'], 'encrypt' => true],
    ['path' => Config::ENDPOINT_PATH, 'value' => $ossValues['ossEndpoint']],
    ['path' => Config::BUCKET_PATH, 'value' => $ossValues['ossBucket']],
    ['path' => 'rezolve/cdn/backend', 'value' => 'OSS']
];
