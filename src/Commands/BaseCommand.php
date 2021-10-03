<?php

namespace Apih\LangHelper\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\Translator;
use Symfony\Component\Finder\Finder;

class BaseCommand extends Command
{
    protected Filesystem $filesystem;
    protected Finder $finder;
    protected Translator $translator;

    /**
     * Create a new command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $filesystem
     * @param  \Symfony\Component\Finder\Finder  $finder
     * @param  \Illuminate\Translation\Translator  $translator
     * @return void
     */
    public function __construct(Filesystem $filesystem, Finder $finder, Translator $translator)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
        $this->finder = $finder;
        $this->translator = $translator;
    }

    /**
     * Print the line divider.
     *
     * @param  int  $length
     * @return void
     */
    public function divider(int $length = 80)
    {
        $this->line(str_repeat('-', $length));
    }

    /**
     * Print the duration taken by the command to complete its process.
     *
     * @return void
     */
    public function runningTimeInfo()
    {
        $this->info(trans_choice('[0,1] Run in :count second|[2,*] Run in :count seconds', number_format(microtime(true) - LARAVEL_START, 3)));
    }
}
