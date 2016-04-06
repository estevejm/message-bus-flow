<?php

namespace EJM\Flow\Network;

use EJM\Flow\Common\Set;
use EJM\Flow\Network\Node;
use EJM\Flow\Network\Node\Command;
use EJM\Flow\Network\Node\Event;
use EJM\Flow\Network\Node\MessagePublisher;

class Blueprint implements NetworkInterface
{
    /**
     * @var Set
     */
    private $commands;

    /**
     * @var Set
     */
    private $events;

    /**
     * @var Set
     */
    private $messagePublishers;

    public function __construct()
    {
        $this->commands = new Set();
        $this->events = new Set();
        $this->messagePublishers = new Set();
    }

    /**
     * {@inheritdoc}
     */
    public function getNodes()
    {
        return array_merge(
            $this->commands->getAll(),
            $this->events->getAll(),
            $this->messagePublishers->getAll()
        );
    }

    /**
     * @param Command $command
     * @return $this
     */
    public function addCommand(Command $command)
    {
        $this->commands->add($command->getId(), $command);

        return $this;
    }

    /**
     * @return Command[]
     */
    public function getCommands()
    {
        return $this->commands->getAll();
    }

    /**
     * @param string $id
     * @return Command
     */
    public function getCommand($id)
    {
        return $this->commands->get($id);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function hasCommand($id)
    {
        return $this->commands->has($id);
    }

    /**
     * @param Event $event
     * @return $this
     */
    public function addEvent(Event $event)
    {
        $this->events->add($event->getId(), $event);

        return $this;
    }

    /**
     * @return Event[]
     */
    public function getEvents()
    {
        return $this->events->getAll();
    }

    /**
     * @param string $id
     * @return Event
     */
    public function getEvent($id)
    {
        return $this->events->get($id);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function hasEvent($id)
    {
        return $this->events->has($id);
    }

    /**
     * @param MessagePublisher $messagePublisher
     * @return $this
     */
    public function addMessagePublisher(MessagePublisher $messagePublisher)
    {
        $this->messagePublishers->add($messagePublisher->getId(), $messagePublisher);

        return $this;
    }

    /**
     * @return MessagePublisher[]
     */
    public function getMessagePublishers()
    {
        return $this->messagePublishers->getAll();
    }

    /**
     * @param string $id
     * @return MessagePublisher
     */
    public function getMessagePublisher($id)
    {
        return $this->messagePublishers->get($id);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function hasMessagePublisher($id)
    {
        return $this->messagePublishers->has($id);
    }
}
