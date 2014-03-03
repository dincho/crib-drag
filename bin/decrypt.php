<?php

error_reporting(E_ALL);

require_once(dirname(__FILE__) . '/../src/CribDrag.php');

if ($argc < 3) {
    printf("Usage: %s key ciphertexts_directory\n", $argv[0]);
    exit(1);
}

$key = pack("H*", $argv[1]);
$dir = new DirectoryIterator($argv[2]);

foreach ($dir as $fileinfo) {
    if (!$fileinfo->isDot()) {
        $ciphertext = pack("H*", file_get_contents($fileinfo->getPathName()));
        $plaintext = CribDrag::strxor($key, $ciphertext);
        printf("%s ---> %s\n", $fileinfo->getFilename(), $plaintext);
    }
}
