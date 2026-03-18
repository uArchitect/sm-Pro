<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class AuditTranslations extends Command
{
    protected $signature = 'translations:audit {--fix : Show suggestions for missing keys}';
    protected $description = 'Compare EN and TR translation files and report missing keys';

    public function handle(): int
    {
        $langPath = lang_path();
        $locales = ['en', 'tr'];
        $errors = 0;

        $enFiles = glob($langPath . '/en/*.php');

        foreach ($enFiles as $enFile) {
            $file = basename($enFile, '.php');
            $trFile = $langPath . '/tr/' . $file . '.php';

            if (!file_exists($trFile)) {
                $this->error("  MISSING FILE: lang/tr/{$file}.php");
                $errors++;
                continue;
            }

            $enKeys = Arr::dot(require $enFile);
            $trKeys = Arr::dot(require $trFile);

            $missingInTr = array_diff_key($enKeys, $trKeys);
            $missingInEn = array_diff_key($trKeys, $enKeys);

            if (!empty($missingInTr)) {
                $errors += count($missingInTr);
                $this->warn("  [{$file}.php] EN'de var, TR'de yok:");
                foreach ($missingInTr as $key => $value) {
                    $this->line("    - {$file}.{$key} => \"{$value}\"");
                }
            }

            if (!empty($missingInEn)) {
                $errors += count($missingInEn);
                $this->warn("  [{$file}.php] TR'de var, EN'de yok:");
                foreach ($missingInEn as $key => $value) {
                    $this->line("    - {$file}.{$key} => \"{$value}\"");
                }
            }

            if (empty($missingInTr) && empty($missingInEn)) {
                $this->info("  [{$file}.php] OK — " . count($enKeys) . ' key(s)');
            }
        }

        $trFiles = glob($langPath . '/tr/*.php');
        foreach ($trFiles as $trFile) {
            $file = basename($trFile, '.php');
            if (!file_exists($langPath . '/en/' . $file . '.php')) {
                $this->error("  MISSING FILE: lang/en/{$file}.php");
                $errors++;
            }
        }

        $this->newLine();
        if ($errors === 0) {
            $this->info('All translation keys are in sync between EN and TR.');
            return self::SUCCESS;
        }

        $this->error("{$errors} translation issue(s) found.");
        return self::FAILURE;
    }
}
