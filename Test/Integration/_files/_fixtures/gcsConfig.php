<?php

use Rezolve\APISalesV4\Model\GCS\Config;

$paramFile = __DIR__ . '/../../../../../../../shellscripts/installParameters.bash';

if (!file_exists($paramFile)) {
    throw new \Exception("install param file does not exist");
}

$fileHandle = fopen($paramFile, 'r');

$gcsValues = [];

while ($line = fgets($fileHandle)) {
    if (strpos($line, 'gcs') !== 0) {
        continue;
    }

    preg_match("#^([^=]*)='([^']*)'$#", $line, $matches);

    $gcsValues[$matches[1]] = $matches[2];
}

$gcsConfigArray = [
    ['path' => Config::GCS_JSON_KEYFILE_PATH, 'value' => $gcsValues['gcsJsonKeyFile'], 'encrypt' => true],
    ['path' => Config::GCS_BUCKET_PATH, 'value' => $gcsValues['gcsBucket']],
    ['path' => 'rezolve/cdn/backend', 'value' => 'GCS']
];
