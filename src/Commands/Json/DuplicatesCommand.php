<?php

namespace Apih\LangHelper\Commands\Json;

use Apih\LangHelper\Commands\BaseCommand;

class DuplicatesCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:json:duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find duplicated translation messages in JSON files';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $files = $this->finder->in(app()->langPath())->files()->name('*.json');

        // Terminate early if there is no file
        if (count($files) === 0) {
            $this->info(__('No language file is found.'));

            return;
        }

        // Find duplicates in each file
        foreach ($files as $file) {
            $messages = json_decode($file->getContents(), true);
            $result = [];

            foreach ($messages as $message) {
                if (isset($result[$message])) {
                    continue;
                }

                $duplicates = array_filter($messages, function ($value) use ($message) {
                    return $value === $message;
                });

                if (count($duplicates) > 1) {
                    $result[$message] = array_keys($duplicates);
                }
            }

            $resultCount = count($result);
            $padCounterLength = strlen($resultCount);
            $spaces = str_repeat(' ', $padCounterLength);
            $counter = 1;

            $this->info(__('Same messages with different keys found in :file.', ['file' => basename($file)]));
            $this->divider();

            foreach ($result as $message => $items) {
                $paddedCounter = str_pad($counter++, $padCounterLength, ' ', STR_PAD_LEFT);

                $this->line("{$paddedCounter}. <fg=cyan>{$message}</>");

                foreach ($items as $item) {
                    $this->line("{$spaces}  - <fg=yellow>{$item}</>");
                }
            }

            $this->newLine();
        }

        $this->divider();
        $this->runningTimeInfo();
    }
}
