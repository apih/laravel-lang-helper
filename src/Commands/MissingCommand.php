<?php

namespace Apih\LangHelper\Commands;

use ParseError;

class MissingCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:missing
                {--locale= : Specify target locale}
                {--dirs= : Specify target directories}
                {--add-dirs= : Add directories to the default target directories}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find missing translation messages in the files';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $options = $this->options();

        // Target locale
        $locale = $options['locale'] ?? app()->getLocale();

        // Default directories
        $dirs = ['app', 'config', 'database', 'public', 'resources', 'routes'];

        if ($options['dirs']) {
            $dirs = array_map('trim', explode(',', $options['dirs']));
        }

        if ($options['add-dirs']) {
            $dirs = array_merge($dirs, array_map('trim', explode(',', $options['add-dirs'])));
        }

        sort($dirs);

        // Scan all defined messages based on regex pattern
        $this->info(__('Scanning the following directories:'));
        $this->line(implode(', ', $dirs));
        $this->newLine();

        $result = [];

        foreach ($dirs as $dir) {
            if ($this->filesystem->missing($dir)) {
                continue;
            }

            $files = $this->filesystem->allFiles($dir);

            foreach ($files as $file) {
                $pattern = '/([^0-9a-zA-Z]__|trans|trans_choice|@lang)\(\s*?([\'\"])(.*?)([\'\"])\s*?[\)?,?]/s';
                $contents = $file->getContents();

                if (!preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER, 0)) {
                    continue;
                }

                foreach ($matches as $match) {
                    try {
                        $key = eval("return {$match[2]}{$match[3]}{$match[4]};");
                    } catch (ParseError $e) {
                        continue;
                    }

                    if ($this->translator->hasForLocale($key, $locale)) {
                        continue;
                    }

                    $lineCount = substr_count($contents, "\n", 0, strpos($contents, $match[0])) + 1;

                    $result[$key] = [
                        'file' => $file,
                        'line' => $lineCount,
                        'key' => $key,
                    ];
                }
            }
        }

        // Display the result
        $resultCount = count($result);
        $padCounterLength = strlen($resultCount);
        $counter = 1;

        $this->info(trans_choice('[0] Found no message.|[1] Found 1 message.|[2,*] Found :count messages.', $resultCount));
        $this->divider();

        foreach ($result as $item) {
            $this->line(sprintf(
                '%s. <fg=cyan>%s</><fg=yellow>(%d)</><fg=white>: <fg=bright-cyan>%s</>',
                str_pad($counter++, $padCounterLength, ' ', STR_PAD_LEFT),
                $item['file'],
                $item['line'],
                $item['key'],
            ));
        }

        if ($resultCount > 0) {
            $this->newLine();
            $this->divider();
        }

        $this->runningTimeInfo();
    }
}
