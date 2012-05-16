<?php

namespace Core;

/**
 * Listen for ping, send a pong
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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use \Packet;

class PingSubscriber implements EventSubscriberInterface {

    /**
     * Says what you want to listen for
     *
     * @see parent::getSubscribedEvents
     * @return array
     */
    static public function getSubscribedEvents() {
        return array(
            'command.ping' => 'onPing'
        );
    }

    /**
     * Event will always be a GenericEvent
     *
     * @param Symfony\Component\EventDispatcher\GenericEvent $event
     */
    public function onPing($event) {
        // output will spit sutff out to the command line
        $event->getArgument('output')->writeln('Someone sent me a ping!');

        // Create and send a packet
        $packet = new Packet();
        $packet->setCommand('PONG');
        // The Subject is a Packet object
        $packet->setParameters($event->getSubject()->getParameters());

        // Send the packet to the server
        $event->getArgument('socket')->writeln($packet->getRaw());
    }

}