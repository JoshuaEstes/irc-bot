<?php

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
class Packet {

    /**
     * @var string
     */
    protected $raw, $prefix, $command, $parameters, $trailing;

    public function __construct($raw = null) {
        if (null !== $raw) {
            $this->setRaw($raw);
        }
    }

    protected function parse() {
        // :<prefix> <command> <params> :<trailing>
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

    public function setRaw($raw) {
        $this->raw = $raw;
        $this->parse();
    }

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
            $raw .= ':' . $this->trailing;
        }
        $raw = trim($raw);
        return $raw;
    }

    public function setPrefix($prefix) {
        $this->prefix = $prefix;
    }

    public function getPrefix() {
        return $this->prefix;
    }

    public function setCommand($command) {
        $this->command = strtoupper($command);
    }

    public function getCommand() {
        return $this->command;
    }

    public function setParameters($parameters) {
        $this->parameters = $parameters;
    }

    public function getParameters() {
        return $this->parameters;
    }

    public function setTrailing($trailing) {
        $this->trailing = $trailing;
    }

    public function getTrailing() {
        return $this->trailing;
    }

}