<?php

use Rezolve\APISalesV4\Model\Amazon\Config;

$paramFile = __DIR__ . '/../../../../../../../shellscripts/installParameters.bash';

if (!file_exists($paramFile)) {
    throw new \Exception("install param file does not exist");
}

$fileHandle = fopen($paramFile, 'r');

$s3Values = [];

while ($line = fgets($fileHandle)) {
    if (strpos($line, 's3') !== 0) {
        continue;
    }
    preg_match("#^([^=]*)='([^']*)'#", $line, $matches);
    $s3Values[$matches[1]] = $matches[2];
}

$s3ConfigArray = [
    ['path' => Config::KEY_PATH, 'value' => $s3Values['s3Key'], 'encrypt' => true],
    ['path' => Config::SECRET_PATH, 'value' => $s3Values['s3Secret'], 'encrypt' => true],
    ['path' => Config::REGION_PATH, 'value' => $s3Values['s3Region']],
    ['path' => Config::BUCKET_PATH, 'value' => $s3Values['s3Bucket']],
];
