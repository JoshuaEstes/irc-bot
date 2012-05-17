<?php

/**
 * Test to make sure that the packet does what it should do
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

require_once __DIR__ . '/../lib/Packet.php';

class PacketTest extends \PHPUnit_Framework_TestCase {

    public function testPacket() {
        // :<prefix> <command> <params> :<trailing>
        $packet = new Packet(':hubbard.freenode.net NOTICE * :*** Looking up your hostname...');
        $this->assertEquals('hubbard.freenode.net', $packet->getPrefix());
        $this->assertEquals('NOTICE', $packet->getCommand());
        $this->assertEquals('*', $packet->getParameters());
        $this->assertEquals('*** Looking up your hostname...', $packet->getTrailing());

        $packet = new Packet('USER guest tolmoon tolsun :Ronnie Reagan');
        $this->assertEquals(null, $packet->getPrefix());
        $this->assertEquals('USER', $packet->getCommand());
        $this->assertEquals('guest tolmoon tolsun', $packet->getParameters());
        $this->assertEquals('Ronnie Reagan', $packet->getTrailing());

        $packet = new Packet(':f1gm3nt!~f1gm3nt@72.242.184.210 PRIVMSG bobobot :!join #test-chan');
        $this->assertEquals('f1gm3nt!~f1gm3nt@72.242.184.210', $packet->getPrefix());
        $this->assertEquals('PRIVMSG', $packet->getCommand());
        $this->assertEquals('bobobot', $packet->getParameters());
        $this->assertEquals('!join #test-chan', $packet->getTrailing());
    }

}