<?php
/**
 * Standalone translation audit script.
 * Compares EN and TR translation files and reports missing keys.
 * Runs without Laravel (CI-safe).
 *
 * Usage: php scripts/audit-translations.php
 * Exit code 0 = all keys in sync, 1 = mismatches found
 */

$langDir = __DIR__ . '/../lang';
$locales = ['en', 'tr'];
$errors  = 0;

function dotKeys(array $arr, string $prefix = ''): array {
    $result = [];
    foreach ($arr as $key => $value) {
        $full = $prefix === '' ? (string)$key : $prefix . '.' . $key;
        if (is_array($value)) {
            $result = array_merge($result, dotKeys($value, $full));
        } else {
            $result[$full] = $value;
        }
    }
    return $result;
}

$enFiles = glob($langDir . '/en/*.php');
if (empty($enFiles)) {
    fwrite(STDERR, "No EN translation files found in {$langDir}/en/\n");
    exit(1);
}

foreach ($enFiles as $enFile) {
    $file   = basename($enFile, '.php');
    $trFile = $langDir . '/tr/' . $file . '.php';

    if (!file_exists($trFile)) {
        fwrite(STDERR, "  MISSING: lang/tr/{$file}.php\n");
        $errors++;
        continue;
    }

    $enKeys = dotKeys(require $enFile);
    $trKeys = dotKeys(require $trFile);

    $missingInTr = array_diff_key($enKeys, $trKeys);
    $missingInEn = array_diff_key($trKeys, $enKeys);

    if (!empty($missingInTr)) {
        $errors += count($missingInTr);
        fwrite(STDERR, "  [{$file}.php] In EN but missing in TR:\n");
        foreach ($missingInTr as $key => $value) {
            fwrite(STDERR, "    - {$file}.{$key}\n");
        }
    }

    if (!empty($missingInEn)) {
        $errors += count($missingInEn);
        fwrite(STDERR, "  [{$file}.php] In TR but missing in EN:\n");
        foreach ($missingInEn as $key => $value) {
            fwrite(STDERR, "    - {$file}.{$key}\n");
        }
    }

    if (empty($missingInTr) && empty($missingInEn)) {
        echo "  [{$file}.php] OK — " . count($enKeys) . " key(s)\n";
    }
}

$trFiles = glob($langDir . '/tr/*.php');
foreach ($trFiles as $trFile) {
    $file = basename($trFile, '.php');
    if (!file_exists($langDir . '/en/' . $file . '.php')) {
        fwrite(STDERR, "  MISSING: lang/en/{$file}.php\n");
        $errors++;
    }
}

echo "\n";
if ($errors === 0) {
    echo "All translation keys are in sync between EN and TR.\n";
    exit(0);
}

fwrite(STDERR, "{$errors} translation issue(s) found.\n");
exit(1);
