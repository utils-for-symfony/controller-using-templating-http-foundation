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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

// Symfony\Component\Process hidden dependecy

class PathFinderEchoCommand extends Command
{
    const NAME = 'pathfinderecho';
    const DESCRIPTION = 'Echo first file in finder results for given path';
    // const ARGUMENTS = ['path'=>[InputArgument::REQUIRED, 'The path']]; // Not used! (Default values may depend of context) Requires recent PHP version.

    protected function configure()
    {
        $this->setName(self::NAME)
            ->setDescription(self::DESCRIPTION)
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'The path'
            )
            ->addOption(
                'files',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Results must be files.',
                True
            )
            ->addOption(
                'dotfiles',
                null,
                InputOption::VALUE_OPTIONAL,
                'To include dot files',
                True
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new Finder();
        $finder->in(getcwd())->path($input->getArgument('path'));
        switch (PHP_OS) {
            case 'Linux':
                $finder-->in(getenv('HOME'));
                break;
            // case '':
            case 'WINNT': // Wine
                $finder->in(getenv('HOMEDRIVE').getenv('HOMEPATH'));
                echo 'HOMEDRIVE HOMEPATH', getenv('HOMEDRIVE'), getenv('HOMEPATH'), PHP_EOL;
                echo 'APPDATA', getenv('APPDATA'), PHP_EOL;
                echo 'LOCALAPPDATA', getenv('LOCALAPPDATA'), PHP_EOL;
                echo 'USERPROFILE', getenv('USERPROFILE'), PHP_EOL;
                // https://en.wikipedia.org/wiki/Environment_variable#Windows
                break;
            default:
                echo 'Warning: Unrecognized Operating System: ', PHP_OS, PHP_EOL;
        }
        if ($input->getOption('files')) {
            $finder->files();
        }
        if ($input->getOption('dotfiles')) {
            $finder->ignoreDotFiles(false);
        }

        $iterator = $finder->getIterator();
        $iterator->rewind();
        $file = $iterator->current();

        echo $input->getArgument('path'), PHP_EOL;
        echo PHP_OS, DIRECTORY_SEPARATOR, PHP_BINARY, PHP_EOL;
        // In PHP 7.2 PHP_OS_FAMILY will be available. This should go in a kind of phpinfo module.

        if (null === $file) {

            return;
        }
        if ($file->isExecutable()) {
            echo 'Executable: '.$file->getRelativePathname().PHP_EOL;
            echo 'Executable: '.$file->getRealPath().PHP_EOL;

            return;
        } elseif ($file->isReadable()) {
            echo 'Readeable: '.$file->getRelativePathname().PHP_EOL;
            echo 'Readeable: '.$file->getRealPath().PHP_EOL;

            return;
        }
        echo 'Problem: '.$file->getRelativePathname().PHP_EOL;
        echo 'Problem: '.$file->getRealPath().PHP_EOL;
    }
}
