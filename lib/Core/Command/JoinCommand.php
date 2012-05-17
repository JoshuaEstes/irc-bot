<?php

namespace Core\Command;

/**
 * Description
 *
 * @package
 * @subpackage
 * @author     Joshua Estes
 * @copyright  2012
 * @version    0.1.0
 * @category
 * @license
 *
 */

use \Packet;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class JoinCommand extends Command {

    protected function configure() {
        $this->addArgument('channel', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $packet = new Packet();
        $packet->setCommand('join');
        $packet->setTrailing($input->getArgument('channel'));
        $output->writeln($packet->getRaw());
    }

}