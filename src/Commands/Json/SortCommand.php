<?php

namespace Apih\LangHelper\Commands\Json;

use Apih\LangHelper\Commands\BaseCommand;

class SortCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:json:sort';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sort messages in JSON language files';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $files = $this->finder->in(app()->langPath())->files()->name('*.json');
        $files = iterator_to_array($files, false);

        // Terminate early if no file is found
        if (count($files) === 0) {
            $this->info(__('No language file is found.'));

            return;
        }

        // Sort messages in each file
        foreach ($files as $file) {
            $messages = json_decode($file->getContents(), true);

            // Case-insensitive sort
            uksort($messages, function ($a, $b) {
                return strcasecmp($a, $b);
            });

            $messages = json_encode($messages, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            $this->filesystem->put($file, $messages . PHP_EOL);
        }

        // Display the result
        $this->info(__('The content of the following language files has been sorted.'));
        $this->divider();

        foreach ($files as $key => $file) {
            $this->line(sprintf('%d. <fg=cyan>%s</>', $key + 1, 'resources/lang/' . $file->getRelativePathname()));
        }

        $this->newLine();
        $this->divider();
        $this->runningTimeInfo();
    }
}
