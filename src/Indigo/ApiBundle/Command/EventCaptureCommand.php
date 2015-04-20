<?php

namespace Indigo\ApiBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Indigo\ApiBundle\Service\Manager\ApiManager;

class EventCaptureCommand extends ContainerAwareCommand
{


    /**
     *
     */
    protected function configure()
    {

        $this->setName('event:dump')
            ->setDescription('Dumps events from table API')
            ->addArgument('rows', InputArgument::OPTIONAL, 'Defines how many rows to dump', 100)
            ->addArgument('from-id', InputArgument::OPTIONAL, 'record id from which get rows', 'last')//from-id=20
            ->addArgument('from-ts', InputArgument::OPTIONAL, 'Set timestamp to start dump from')
            ->addArgument('tills-ts', InputArgument::OPTIONAL, 'Set timestamp to stop dump till time');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $query = ['rows' => $input->getArgument('rows')];
        if ($input->getArgument('from-id') == 'last') {

            $tableKey = 1;
            $em = $this->getContainer()->get('doctrine.orm.entity_manager');
            $er = $em->getRepository('IndigoGameBundle:TableStatus');
            $tableStatus = $er->findOneByTableId($tableKey);

            if ($tableStatus !== null) {

                $query['from-id'] = $tableStatus->getLastApiRecordId();
            } else {

                $query['from-id'] = 1;
            }
        }
        if ($val = $input->getArgument('from-ts')) {

            $query['from-ts'] = $val;
            if ($val = $input->getArgument('tills-ts')) {

                $query['till-ts'] = $val;
            }
        }

        $logger = $this->getContainer()->get('logger');
        $manager = $this->getContainer()->get('indigo_api.connection_manager');

        try {
            $isDevEnv = ($this->getContainer()->getParameter('kernel.environment') == 'dev');

            $eventList = $manager->getEvents(
                $tableKey,
                $query,
                $isDevEnv
            );

            if ($eventList) {

                $eventLogicManager = $this->getContainer()->get('indigo_table.event_flow_logic_manager');
                $eventLogicManager->analyzeEventFlow($eventList);
            } else {

                $logger->addInfo('No events from API');
            }
        } catch (\Exception $e) {

            $logger->addError(sprintf('Failed API response: %s, in: %s:%u ', $e->getMessage(),$e->getFile(), $e->getLine()));
            exit(1);
        }

    }
}