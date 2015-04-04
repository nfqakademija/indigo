<?php

namespace Indigo\API\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Indigo\API\Manager\Manager;

class EventCaptureCommand extends ContainerAwareCommand
{

    const API_URL = 'http://wonderwall.ox.nfq.lt/kickertable/api/v1/events';
    const API_USERNAME = 'nfq';
    const API_PASSWORD = 'labas';

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

        $manager = new Manager(self::API_URL, [
            'auth' => [self::API_USERNAME, self::API_PASSWORD],
            'query' => $query
            ]
        );
        $el = $manager->getEvents();
        var_dump($el);
        return true;
    }
}