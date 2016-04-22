<?php

namespace EJM\Flow\Network\Builder\AssemblyStage;

use EJM\Flow\Collector\MessagesToPublishCollector;
use EJM\Flow\Network\Blueprint;
use EJM\Flow\Network\Builder\AssemblyStage;
use EJM\Flow\Network\Node\Event;

class AddPublishedMessages implements AssemblyStage
{
    /**
     * @var MessagesToPublishCollector $collector
     */
    private $collector;

    /**
     * @param MessagesToPublishCollector $collector
     */
    public function __construct(MessagesToPublishCollector $collector)
    {
        $this->collector = $collector;
    }

    /**
     * @param Blueprint $blueprint
     */
    public function assemble(Blueprint $blueprint)
    {
        foreach($blueprint->getPublishers() as $publisher) {
            $messageIds = $this->collector->collect($publisher->getClassName());
            foreach ($messageIds as $messageId) {
                if ($blueprint->hasCommand($messageId)) {
                    $publisher->publishes($blueprint->getCommand($messageId));
                    continue;
                }

                if (!$blueprint->hasEvent($messageId)) {
                    $blueprint->addEvent(new Event($messageId));
                }

                $publisher->publishes($blueprint->getEvent($messageId));
            }
        }
    }
}
