<?php

namespace Core;

/**
 * Core
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
use Core\Command\JoinCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class Core implements EventSubscriberInterface {

    /**
     * Says what you want to listen for
     *
     * @see parent::getSubscribedEvents
     * @return array
     */
    static public function getSubscribedEvents() {
        return array(
            'connect_post' => 'onPostConnect',
            'command.ping' => 'onPing',
            'command.notice' => 'onNotice',
            'command.privmsg' => 'onPrivmsg',
            'command.mode' => 'onNotice',
            'command.001' => 'onNotice',
            'command.002' => 'onNotice',
            'command.003' => 'onNotice',
            'command.004' => 'onNotice',
            'command.005' => 'onNotice',
            'command.251' => 'onNotice',
            'command.252' => 'onNotice',
            'command.253' => 'onNotice',
            'command.253' => 'onNotice',
            'command.255' => 'onNotice',
            'command.265' => 'onNotice',
            'command.266' => 'onNotice',
            'command.250' => 'onNotice',
            'command.375' => 'onNotice',
            'command.372' => 'onNotice',
            'command.376' => 'onNotice',
            'command.!join' => 'requestJoin'
        );
    }

    /**
     * @param Symfony\Component\EventDispatcher\GenericEvent $event
     */
    public function onPrivmsg($event) {
        $command = new StringInput($event->getArgument('packet')->getTrailing());
        if (substr($command->getFirstArgument(), 0, 1) === '!' || substr($command->getFirstArgument(), 0, 1) === '@') {
            $eventName = sprintf('command.%s', strtolower($command->getFirstArgument()));
            $event->getSubject()->getDispatcher()->dispatch($eventName, new GenericEvent($event->getSubject(), array(
                    'packet' => $event->getArgument('packet'),
                    'command' => $command
                )));
        }
    }

    /**
     * @param Symfony\Component\EventDispatcher\GenericEvent $event
     */
    public function requestjoin($event) {
        /* @var $packet \Packet */
        $packet = $event->getArgument('packet');
        if (\in_array($packet->getNick(), $event->getSubject()->getConfig('trusted_users'))) {
            $command = new JoinCommand('!join');
            try {
                $command->run(new StringInput(trim(str_replace('!join', '', $packet->getTrailing()))), $event->getSubject()->getSocket());
            }
            catch (Exception $e) {
                $event->getSubject()->getOutput()->writeln($e->getMessage());
            }
        }
    }

    /**
     * @param Symfony\Component\EventDispatcher\GenericEvent $event
     */
    public function onNotice($event) {
        $event->getSubject()->getOutput()->writeln($event->getArgument('packet')->getTrailing());
    }

    /**
     * @param Symfony\Component\EventDispatcher\GenericEvent $event
     */
    public function onPostConnect($event) {
        $config = $event->getSubject()->getConfig();

        // Send the user command
        $packet = new Packet();
        $packet->setCommand('USER');
        $packet->setParameters(array(
            $config['irc']['username'],
            $config['irc']['hostname'],
            $config['irc']['servername'],
        ));
        $packet->setTrailing($config['irc']['realname']);
        $event->getSubject()->getSocket()->writeln($packet->getRaw());

        // send the nick command
        $packet = new Packet();
        $packet->setCommand('NICK');
        $packet->setParameters($config['irc']['nickname']);
        $event->getSubject()->getSocket()->writeln($packet->getRaw());
    }

    /**
     * Event will always be a GenericEvent
     *
     * @param Symfony\Component\EventDispatcher\GenericEvent $event
     */
    public function onPing($event) {
        // Create and send a packet
        $packet = new Packet();
        $packet->setCommand('PONG');
        // The Subject is a Packet object
        $packet->setParameters($event->getArgument('packet')->getParameters());

        // Send the packet to the server
        $event->getSubject()->getSocket()->writeln($packet->getRaw());
    }

}