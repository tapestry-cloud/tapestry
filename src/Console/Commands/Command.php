<?php namespace Tapestry\Console\Commands;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends SymfonyCommand
{
    /** @var  InputInterface */
    protected $input;
    /** @var  OutputInterface */
    protected $output;
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        return (int) $this->fire();
    }
    protected function info($string)
    {
        $this->output->writeln("<info>{$string}</info>");
    }
    protected function error($string)
    {
        $this->output->writeln('<error>[!]</error> ' . $string);
    }
    /**
     * @return int
     */
    abstract protected function fire();
}