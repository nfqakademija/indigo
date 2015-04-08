<?php

namespace Indigo\ApiBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Indigo\ApiBundle\Service\Manager\Manager;

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
     * @return
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {


        $query = ['rows' => $input->getArgument('rows')];
        if ($input->getArgument('from-id') == 'last') {

            $em = $this->getContainer()->get('doctrine.orm.entity_manager');
            $repo = $em->getRepository('IndigoApiBundle:Param');
            $lastEventParam = $repo->findOneBy(['param' => Manager::LAST_RECORD_ID]);
            if ($lastEventParam !== null) {
                $query['from-id'] = $lastEventParam->getValue();
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
                1,
                $query,
                $isDevEnv
            );

            if ($eventList) {
                $eventLogicManager = $this->getContainer()->get('indigo_api.event_logic_manager');
                $eventLogicManager->analyzeEventFlow($eventList);
            } else {
                $logger->addInfo('No events from API');
            }
        } catch (\Exception $e) {
            $logger->addError('Failed API response: ' . $e->getMessage());
            exit(1);
        }

    }
}