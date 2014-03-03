<?php

error_reporting(E_ALL);

require_once(dirname(__FILE__) . '/../src/CribDrag.php');

if ($argc < 3) {
    printf("Usage: %s cipherfile1 cipherfile2\n", $argv[0]);
    exit(1);
}

$c1 = pack("H*", file_get_contents($argv[1]));
$c2 = pack("H*", file_get_contents($argv[2]));

$charsetRegex = "/^[a-zA-Z0-9\-,\. ]+$/";
$dragger = new CribDrag($c1, $c2);

do {
    passthru('clear');
    foreach ($dragger->getPlaintexts() as $idx => $plaintext) {
        printf("Plaintext #%d: %s\n", $idx, $plaintext);
    }

    echo "Enter crib: ";
    $crib = trim(fgets(STDIN), "\n");

    if ("" == $crib) {
        break;
    }

    $dragger->setCrib($crib);

    foreach ($dragger->generateCandidates() as $idx => $candidate) {
        if (preg_match($charsetRegex, $candidate)) {
            printf("---> #%d: '%s'\n", $idx, $candidate);
        } else {
            printf("     #%d: '%s'\n", $idx, $candidate);
        }
    }

    echo "\nChoose position: ";
    $pos = trim(fgets(STDIN));

    if ("" === $pos) {
        echo "No changes\n";
        continue;
    }

    if (!ctype_digit($pos)) {
        echo "Invalid position.\n";
        continue;
    }

    echo "To which plaintext the crib should be applied? ";
    $idx = trim(fgets(STDIN));

    $dragger->applyCrib((int) $idx, (int) $pos);
} while (true);

echo "Key: " . bin2hex($dragger->getKey()) . "\n";