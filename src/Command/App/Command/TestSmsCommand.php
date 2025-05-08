<?php

namespace App\Command\App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'App\Command\TestSmsCommand',
    description: 'Add a short description for your command',
)]
class TestSmsCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('phone_number', InputArgument::REQUIRED, 'The phone number to send the test SMS to')
            ->addArgument('message', InputArgument::REQUIRED, 'The message to send in the test SMS')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $phoneNumber = $input->getArgument('phone_number');
        $message = $input->getArgument('message');

        if ($phoneNumber && $message) {
            $io->note(sprintf('Sending test SMS to %s with message: %s', $phoneNumber, $message));
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
