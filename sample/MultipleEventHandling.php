<?php
namespace sample;

require_once __DIR__ . '/../vendor/autoload.php';

use predaddy\messagehandling\MessageBase;
use predaddy\messagehandling\annotation\Subscribe;

class SampleMessage1 extends MessageBase
{
}

class SampleMessage2 extends MessageBase
{
}

class SampleMessageHandler
{
    /**
     * @Subscribe
     */
    public function handleOne(SampleMessage1 $event)
    {
        printf(
            "handleOne: Incoming message %s sent %s\n",
            $event->getMessageIdentifier(),
            $event->getTimestamp()->format('Y-m-d H:i:s')
        );
    }

    /**
     * @Subscribe
     */
    public function handleTwo(SampleMessage2 $event)
    {
        printf(
            "handleTwo: Incoming message %s sent %s\n",
            $event->getMessageIdentifier(),
            $event->getTimestamp()->format('Y-m-d H:i:s')
        );
    }
}

$bus = require_once 'sampleBus.php';
$bus->register(new SampleMessageHandler());

$bus->post(new SampleMessage1());
$bus->post(new SampleMessage2());
$bus->post(new SampleMessage1());
$bus->post(new SampleMessage2());
