<?php

/**
 * Packet that is received/sent from/to the IRC server
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
class Packet {

    /**
     * @var string
     */
    protected $raw, $prefix, $command, $parameters, $trailing;

    /**
     * Create a new packet, can set everything at once or set them one at a time.
     *
     * Syntax:
     *
     *     :<prefix> <command> <parameters> :<trailing>
     *
     * @param string $raw
     */
    public function __construct($raw = null) {
        if (null !== $raw) {
            $this->setRaw($raw);
        }
    }

    public function __toString() {
        return $this->getRaw();
    }

    /**
     * This is used to parse a packet and break it apart into it's various
     * components
     */
    protected function parse() {
        // :<prefix> <command> <parameters> :<trailing>
        $prefixEnd = 0;
        if (substr($this->raw, 0, 1) == ':') {
            $prefixEnd = strpos($this->raw, ' ');
            $this->prefix = substr($this->raw, 1, ($prefixEnd - 1));
        }
        $trailingStart = strpos($this->raw, ' :');
        if ($trailingStart >= 0) {
            $this->trailing = substr($this->raw, ($trailingStart + 2));
        }
        else {
            $trailingStart = strlen($this->raw);
        }
        $commandAndParameters = explode(' ', substr($this->raw, ($prefixEnd ? ($prefixEnd + 1) : ($prefixEnd)), ($trailingStart - ($prefixEnd ? ($prefixEnd) : ($prefixEnd - 1)))));
        $this->command = $commandAndParameters[0];
        unset($commandAndParameters[0]);
        $this->parameters = trim(implode(' ', $commandAndParameters));
    }

    /**
     * Will 'reset' the packet
     *
     * @param string $raw
     */
    public function setRaw($raw) {
        $this->raw = $raw;
        $this->parse();
    }

    /**
     * This will return a string in the format that it needs to be sent to the IRC
     * server
     *
     * @return string
     */
    public function getRaw() {
        // :<prefix> <command> <params> :<trailing>
        $raw = '';
        if (null !== $this->prefix) {
            $raw .= ':' . $this->prefix;
        }
        if (null !== $this->command) {
            $raw .= ' ' . $this->command;
        }
        if (null !== $this->parameters) {
            $raw .= ' ' . $this->parameters;
        }
        if (null !== $this->trailing) {
            $raw .= ' :' . $this->trailing;
        }
        $raw = trim($raw);
        return $raw;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix($prefix) {
        $this->prefix = $prefix;
    }

    /**
     * @return string
     */
    public function getPrefix() {
        return $this->prefix;
    }

    /**
     * Commands are all uppercase, this function will make sure that the command
     * is uppercase if you use this
     *
     * @param string $command
     */
    public function setCommand($command) {
        $this->command = strtoupper($command);
    }

    /**
     * @return string
     */
    public function getCommand() {
        return $this->command;
    }

    /**
     * @param string|array $parameters
     */
    public function setParameters($parameters) {
        if (!is_array($parameters)) {
            $parameters = explode(' ', $parameters);
        }
        $this->parameters = implode(' ', $parameters);
    }

    /**
     * @return string
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * @param string $trailing
     */
    public function setTrailing($trailing) {
        $this->trailing = $trailing;
    }

    /**
     * @return string
     */
    public function getTrailing() {
        return $this->trailing;
    }

    /**
     * @return string
     */
    public function getNick() {
        if (strpos($this->getPrefix(), '!') && strpos($this->getPrefix(), '@')) {
            return substr($this->getPrefix(), 0, strpos($this->getPrefix(), '!'));
        }
        return null;
    }

    /**
     * @return string
     */
    public function getUser() {
        if (strpos($this->getPrefix(), '!') && strpos($this->getPrefix(), '@')) {
            return substr($this->getPrefix(), strpos($this->getPrefix(), '!') + 1, (strpos($this->getPrefix(), '@') - strlen($this->getNick()) - 1));
        }
        return null;
    }

    /**
     * @return string
     */
    public function getHost() {
        if (strpos($this->getPrefix(), '!') && strpos($this->getPrefix(), '@')) {
            return substr($this->getPrefix(), strpos($this->getPrefix(), '@') + 1);
        }
        return null;
    }

}