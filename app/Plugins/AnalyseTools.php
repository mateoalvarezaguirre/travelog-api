<?php

namespace App\Plugins;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class AnalyseTools
{
    public static function analyseChangedFiles(): void
    {
        self::validateChangedFiles();
        $command = ['vendor/bin/phpstan', 'analyse'];
        $command = array_merge($command, self::getChangedFiles());
        $command = array_merge($command, ['--configuration', 'phpstan.neon', '--memory-limit=-1', '-vv']);

        $process = self::executeCommand($command);

        self::displayCommandOutput($process);
    }

    public static function formatChangedFiles(): void
    {
        self::validateChangedFiles();

        $files   = self::getChangedFiles();
        $command = ['vendor/bin/php-cs-fixer', 'fix'];
        $command = array_merge($command, $files);
        $command = array_merge($command, ['--config=.php-cs-fixer.php', '--path-mode=intersection', '-vvv', '--allow-risky=yes']);

        $process = self::executeCommand($command);
        self::displayCommandOutput($process);
        self::stageFormattedFiles($files);
    }

    public static function analyseStagedFiles(): void
    {
        self::validateStagedFiles();

        $command = ['vendor/bin/phpstan', 'analyse'];
        $command = array_merge($command, self::getStagedFiles());
        $command = array_merge($command, ['--configuration', 'phpstan.neon', '--memory-limit=-1', '-vv']);

        $process = self::executeCommand($command);
        self::displayCommandOutput($process);

    }

    public static function formatStagedFiles(): void
    {
        self::validateChangedFiles();

        $files   = self::getChangedFiles();
        $command = ['vendor/bin/php-cs-fixer', 'fix'];
        $command = array_merge($command, $files);
        $command = array_merge($command, ['--config=.php-cs-fixer.php', '--path-mode=intersection', '-vvv', '--allow-risky=yes']);

        $process = self::executeCommand($command);
        self::displayCommandOutput($process);
        self::stageFormattedFiles($files);
    }

    public static function validateStagedFiles(): void
    {
        $process = new Process(['git', 'diff', '--staged', '--name-only', '--diff-filter=d', '--', '**.php']);
        $process->run();

        if (! $process->isSuccessful()) {
            echo $process->getOutput();
            exit(1);
        }

        if (empty($process->getOutput())) {
            echo "No hay archivos .php stageados para analizar. \n";
            exit(0);
        }
    }

    public static function validateChangedFiles(): void
    {
        $process = new Process(['git', 'status', '--short', '--', '**.php']);
        $process->run();

        if (! $process->isSuccessful()) {
            echo $process->getOutput();
            exit(1);
        }

        if (empty($process->getOutput())) {
            echo "No hay archivos .php modificados para analizar. \n";
            exit(0);
        }
    }

    public static function getChangedFiles(): array
    {
        $process = new Process(['git', 'status', '--short', '--', '**.php']);
        $process->run();

        if (! $process->isSuccessful()) {
            echo $process->getOutput();
            exit(1);
        }

        $files = preg_split('/\R+/', $process->getOutput(), flags: PREG_SPLIT_NO_EMPTY);

        if ($files === false) {
            echo $process->getOutput();
            exit(1);
        }

        $changedFiles = (new Collection($files))
            ->mapWithKeys(fn ($file) => [substr($file, 3) => trim(substr($file, 0, 3))])
            ->reject(fn ($status) => $status === 'D')
            ->map(fn ($status, $file) => Str::contains($status, 'R') ? Str::after($file, ' -> ') : $file)
            ->map(function ($file) {
                if (PHP_OS_FAMILY === 'Windows') {
                    $file = str_replace('/', DIRECTORY_SEPARATOR, $file);
                }

                return $file;
            })
            ->values()
            ->all();

        return $changedFiles;
    }

    public static function getStagedFiles(): array
    {
        $process = new Process(['git', 'diff', '--staged', '--name-only', '--diff-filter=d', '--', '**.php']);
        $process->run();

        if (! $process->isSuccessful()) {
            echo $process->getOutput();
            exit(1);
        }

        $files = preg_split('/\R+/', $process->getOutput(), flags: PREG_SPLIT_NO_EMPTY);

        if ($files === false) {
            echo $process->getOutput();
            exit(1);
        }

        $stagedFiles = (new Collection($files))
            ->map(function ($file) {
                if (PHP_OS_FAMILY === 'Windows') {
                    $file = str_replace('/', DIRECTORY_SEPARATOR, $file);
                }

                return $file;
            })
            ->values()
            ->all();

        return $stagedFiles;
    }

    private static function executeCommand(array $command): Process
    {
        $process = new Process($command);
        $process->run();

        return $process;
    }

    private static function displayCommandOutput(Process $process): void
    {
        if (! $process->getOutput()) {
            echo $process->getErrorOutput();
        } else {
            echo $process->getOutput();
        }

        exit($process->getExitCode());
    }

    private static function stageFormattedFiles(array $files): void
    {
        $command = ['git', 'add'];
        $command = array_merge($command, $files);

        $gitAddProcess = new Process($command);
        $gitAddProcess->run();

        if (! $gitAddProcess->isSuccessful()) {
            echo $gitAddProcess->getOutput();
            exit(1);
        }
    }
}
