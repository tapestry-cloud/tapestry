<?php

namespace Tapestry\Console\Commands;

use Tapestry\Exceptions\LockException;
use Tapestry\Tapestry;
use Symfony\Component\Console\Helper\Table;
use Tapestry\Exceptions\InvalidVersionException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

abstract class Command extends SymfonyCommand
{
    /** @var InputInterface */
    protected $input;
    /** @var OutputInterface */
    protected $output;

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->input = $input;
            $this->output = $output;

            $lockFilePathname = $this->input->getOption('site-dir') . DIRECTORY_SEPARATOR . '.lock';
            $lockFile = fopen($lockFilePathname, 'w+');

            if (flock($lockFile, LOCK_EX | LOCK_NB)) {
                $result = (int)$this->fire();
            } else {
                fclose($lockFile);
                throw new LockException('Tapestry is already running; please wait for the previous process to complete or delete the .lock file.');
            }

            fclose($lockFile);
            unlink($lockFilePathname);

            if (defined('TAPESTRY_START') === true && $this->input->getOption('stopwatch')) {
                $this->renderStopwatchReport($output);
            }

            return $result;
        } catch (InvalidVersionException $e) {
            $this->failure('[!] '.$e->getMessage());
            $this->failure('    If you would like to ignore this error, delete the cache file and try again.');
            return 1;
        } catch (\Exception $e) {
            $this->failure($e->getMessage());
            return 1;
        }
    }

    private function renderStopwatchReport(OutputInterface $output)
    {
        $stopwatch = round((microtime(true) - TAPESTRY_START), 3);
        $this->output->writeln('Task complete in: '.$stopwatch.'s ['.file_size_convert(memory_get_usage(true)).'/'.file_size_convert(memory_get_peak_usage(true)).']');

        $this->output->writeln('=== Breakdown by Step ===');
        $table = new Table($output);
        $table->setHeaders(['Name', 'Time (s)', 'Memory Consumption', 'Memory Use', 'Memory Peak']);

        foreach (Tapestry::$profiler->report() as $name => $clock) {
            $table->addRow([
                $name,
                $clock['execution_time'],
                file_size_convert($clock['memory_consumption']),
                file_size_convert($clock['memory_use']),
                file_size_convert($clock['memory_peak']),
            ]);
        }
        $table->render();
    }

    protected function info($string)
    {
        $this->output->writeln("<info>{$string}</info>");
    }

    protected function error($string)
    {
        $this->output->writeln('<error>[!]</error> '.$string);
    }

    protected function failure($string)
    {
        // Because this is a failure e.g. Exception caught we will ignore verbosity
        $this->output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        $this->error($string);
    }

    protected function panic($string, $code = 1)
    {
        $this->error($string);
        exit($code);
    }

    /**
     * @return int
     */
    abstract protected function fire();
}
