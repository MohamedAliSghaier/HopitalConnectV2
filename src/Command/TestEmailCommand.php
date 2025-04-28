<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;


#[AsCommand(
    name: 'test-email',
    description: 'Add a short description for your command',
)]
class TestEmailCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (new Email())
            ->from($_ENV['jalelnasr67@gmail.com'])
            ->to('dalisghaier78910@gmail.com')
            ->subject('Test SMTP')
            ->text('Ceci est un test');
    
        $this->mailer->send($email);
        $output->writeln('Email envoyé avec succès !');
        return Command::SUCCESS;
    }
}
