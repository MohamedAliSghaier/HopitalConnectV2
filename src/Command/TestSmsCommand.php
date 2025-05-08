<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\TwilioService;

class TestSmsCommand extends Command
{
    protected static $defaultName = 'app:test-sms';
    private $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        parent::__construct();
        $this->twilioService = $twilioService;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Test SMS sending functionality')
            ->addArgument('phone', InputArgument::REQUIRED, 'Phone number to send SMS to')
            ->addArgument('message', InputArgument::OPTIONAL, 'Message to send', 'This is a test SMS from your medical application.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $phoneNumber = $input->getArgument('phone');
        $message = $input->getArgument('message');

        try {
            $io->info('Attempting to send SMS...');
            $io->info(sprintf('To: %s', $phoneNumber));
            $io->info(sprintf('Message: %s', $message));

            $result = $this->twilioService->sendSms($phoneNumber, $message);
            
            $io->success(sprintf('SMS sent successfully! SID: %s', $result));
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error(sprintf('Failed to send SMS: %s', $e->getMessage()));
            return Command::FAILURE;
        }
    }
} 