<?php
use predaddy\messagehandling\annotation\AnnotatedMessageHandlerDescriptorFactory;
use predaddy\messagehandling\DefaultFunctionDescriptorFactory;
use predaddy\messagehandling\SimpleMessageBus;

$functionDescFactory = new DefaultFunctionDescriptorFactory();
$bus = new SimpleMessageBus(
    __FILE__,
    new AnnotatedMessageHandlerDescriptorFactory(null, $functionDescFactory),
    $functionDescFactory
);
return $bus;