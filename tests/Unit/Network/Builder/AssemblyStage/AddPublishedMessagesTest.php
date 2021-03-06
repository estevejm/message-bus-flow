<?php

namespace EJM\Flow\Tests\Unit\Network\Builder\AssemblyStage;

use EJM\Flow\Network\Blueprint;
use EJM\Flow\Network\Builder\AssemblyStage\AddPublishedMessages;
use EJM\Flow\Network\Node\Command;
use EJM\Flow\Network\Node\Event;
use EJM\Flow\Network\Node\Handler;
use EJM\Flow\Network\Node\Subscriber;
use PHPUnit_Framework_TestCase;

class AddPublishedMessagesTest extends PHPUnit_Framework_TestCase
{
    const HANDLER_CLASS = '\EJM\Flow\Network\Node\Handler';

    const SUBSCRIBER_CLASS = '\EJM\Flow\Network\Node\Subscriber';

    public function testAssemble()
    {
        $handler1 = new Handler('handler_1', self::HANDLER_CLASS);
        $command1 = new Command('command_1', $handler1);

        $event1 = new Event('event_1');
        $subscriber1 = new Subscriber('subscriber_1', self::SUBSCRIBER_CLASS);
        $subscriber1->subscribesTo($event1);

        $blueprint = new Blueprint();
        $blueprint
            ->addCommand($command1)
            ->addPublisher($handler1)
            ->addEvent($event1)
            ->addPublisher($subscriber1);

        $collector = $this->getMockBuilder('\EJM\Flow\Collector\Collector')
            ->disableOriginalConstructor()
            ->getMock();

        $collector->expects($this->exactly(2))
            ->method('collect')
            ->will($this->returnValueMap([
                [self::HANDLER_CLASS, ['event_1']],
                [self::SUBSCRIBER_CLASS, ['command_1', 'event_2']],
            ]));

        $stage = new AddPublishedMessages($collector);

        $stage->assemble($blueprint);

        $this->assertContains($event1, $blueprint->getPublisher('handler_1')->getMessagesToPublish());
        $this->assertContains($command1, $blueprint->getPublisher('subscriber_1')->getMessagesToPublish());
        $this->assertTrue($blueprint->hasEvent('event_2'));
        $this->assertContains(
            $blueprint->getEvent('event_2'),
            $blueprint->getPublisher('subscriber_1')->getMessagesToPublish()
        );
    }
}
