<?php

/*
 * This file is part of the Symfony-Util package.
 *
 * (c) Jean-Bernard Addor
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

// Symfony\Component\Process hidden dependecy

class WhereCommand extends Command
{
    const NAME = 'where';
    const DESCRIPTION = 'Echo file finder results for system path';
    // const ARGUMENTS = ['path'=>[InputArgument::REQUIRED, 'The path']]; // Not used! (Default values may depend of context) Requires recent PHP version.

    protected function configure()
    {
        $this->setName(self::NAME)
            ->setDescription(self::DESCRIPTION)
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The command filename'
            )
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = (new Finder())->files()->name($input->getArgument('name'))->ignoreDotFiles(false)->ignoreUnreadableDirs();
        foreach (explode(PATH_SEPARATOR, getenv('PATH')) as $directory){
            $finder->in($directory);
        }
        foreach ($finder as $file) {
            var_dump($file->getRealPath());
            if ($file->isExecutable()) {
                echo 'Executable: '.$file->getRealPath().PHP_EOL;
            } elseif ($file->isReadable()) {
                echo 'Readeable: '.$file->getRealPath().PHP_EOL;
            }
        }
}
