<?php

namespace Indigo\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Indigo\API\Manager\Manager;

class EventCaptureCommand extends ContainerAwareCommand
{



    /**
     *
     */
    protected function configure() {

        $this->setName('event:dump')
            ->setDescription('Dumps events from API') 
            ->addArgument('rows', InputArgument::OPTIONAL, 'Defines how many rows to dump', 100)
            ->addArgument('from-ts', InputArgument::OPTIONAL, 'Set timestamp to start dump from')
            ->addArgument('tills-ts', InputArgument::OPTIONAL, 'Set timestamp to stop dump till time');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return
     */
    protected function execute(InputInterface $input, OutputInterface $output) {

        $query = ['rows' => $input->getArgument('rows')];

        if ($val = $input->getArgument('from-ts')) {
            $query['from-ts'] = $val;
            if ($val = $input->getArgument('tills-ts')) {
                $query['till-ts'] = $val;
            }
        }

        $logger = $this->getContainer()->get('logger');
        $manager = $this->getContainer()->get('indigo_main.connection_manager');

        try {
            $eventList = $manager->getEvents($query);

            var_dump($eventList);


        } catch (\Exception $e) {
            $logger->addError('failed api response: '. $e->getMessage());
            exit(1);
        }
    }
}