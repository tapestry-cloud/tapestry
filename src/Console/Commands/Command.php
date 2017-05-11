<?php

namespace Tapestry\Console\Commands;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Tapestry\Tapestry;

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
        $this->input = $input;
        $this->output = $output;
        $result = (int) $this->fire();

        if (defined('TAPESTRY_START') === true && $this->input->getOption('stopwatch')) {
            $this->renderStopwatchReport($output);
        }

        return $result;
    }

    private function renderStopwatchReport(OutputInterface $output) {
        $stopwatch = round((microtime(true) - TAPESTRY_START), 3);
        $this->output->writeln('Task complete in: '.$stopwatch.'s ['.file_size_convert(memory_get_usage(true)).'/'.file_size_convert(memory_get_peak_usage(true)).']');

        $this->output->writeln('=== Breakdown by Step ===');
        $table = new Table($output);
        $table->setHeaders(['Name', 'Time (s)', 'Memory Consumption', 'Memory Use', 'Memory Peak']);

        foreach(Tapestry::$profiler->report() as $clock) {
            $table->addRow([
                $clock['name'],
                $clock['execution_time'],
                file_size_convert($clock['memory_consumption']),
                file_size_convert($clock['memory_use']),
                file_size_convert($clock['memory_peak'])
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
