<?php

namespace Indigo\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PrepareProjectCommand extends ContainerAwareCommand
{

    const DEPS_DIR = 'IndigoDeps';
    const EVENT_DUMP = 'event-dump.sh';
    const INITAL_DUMPT = 'initial.sql';

    /**
     *
     */
    protected function configure()
    {

        $this->setName('init:start')
            ->setDescription('Requirement check and initial data import for "Indigo" project');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . self::DEPS_DIR . DIRECTORY_SEPARATOR;
        $eventDumpPath = $path .  self::EVENT_DUMP;
        $sqlDumpPath = $path .  self::INITAL_DUMPT;

        print ("Execute this: \n");
        printf ("chmod 750 %s\n\n", $eventDumpPath);
        printf("echo '* * * * * www-data  %s' >> /etc/crontab 2>1 >/var/log/indigo.log\n\n", $eventDumpPath);

        printf("mysql -u root -p <database_name> < %s\n\n", $sqlDumpPath);
    }
}