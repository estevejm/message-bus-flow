<?php

namespace Flow\Network\Factory\Step;

use Flow\Network\Blueprint;
use Flow\Network\Factory\Step;
use Flow\Network\Node\Event;
use Flow\Network\Node\Subscriber;

class AddEventsAndSubscribers implements Step
{
    /**
     * @var array
     */
    private $eventSubscribersMap;

    /**
     * @param array $commandHandlerMap
     */
    public function __construct(array $commandHandlerMap)
    {
        // todo: assert map format
        $this->eventSubscribersMap = $commandHandlerMap;
    }

    /**
     * @param Blueprint $blueprint
     */
    public function assemble(Blueprint $blueprint)
    {
        foreach ($this->eventSubscribersMap as $eventId => $eventSubscribers) {
            $blueprint->addEvent(new Event($eventId));
            foreach ($eventSubscribers as $subscriberData) {
                $blueprint->addMessagePublisher(
                    new Subscriber($subscriberData['id'], $subscriberData['class'], $blueprint->getEvent($eventId))
                );
            }
        }
    }
}
