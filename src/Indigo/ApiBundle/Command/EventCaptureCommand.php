<?php

namespace Indigo\ApiBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EventCaptureCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('event:dump')
            ->setDescription('Dumps events from table API')
            ->addArgument('rows', InputArgument::OPTIONAL, 'Defines how many rows to dump', 100)
            ->addArgument('from-id', InputArgument::OPTIONAL, 'record id from which get rows', 'last')
            ->addArgument('demo', InputArgument::OPTIONAL, 'provide demo data', false);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('logger');
        $manager = $this->getContainer()->get('indigo_api.connection_manager');
        $tableKey = 1;
        try {

            $gotEvents = $manager->getEvents($input, $tableKey, (bool)$input->getArgument('demo'));
            if ($gotEvents) {

                $this->getContainer()
                    ->get('indigo_table.timeout_manager')
                    ->checkBetweenResponses($tableKey);
            }
        } catch (\Exception $e) {

            $logger->addError(sprintf('Failed API response: %s, in: %s:%u, %s',
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $e->getTraceAsString())
            );
        }
    }
}