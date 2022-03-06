<?php

namespace Drupal\h22_core\EventSubscriber;

use Drupal\address\Event\AddressEvents;
use Drupal\address\Event\AddressFormatEvent;
use Drupal\address\Event\AvailableCountriesEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddressFormatEventSubscriber implements EventSubscriberInterface {

  public static function getSubscribedEvents() {
    $events[AddressEvents::ADDRESS_FORMAT][] = ['onFormatAlter'];
    return $events;
  }

  public function onFormatAlter(AddressFormatEvent $event) {
    $definition = $event->getDefinition();
    if (str_contains($definition['format'], "\n%addressLine1")) {
      $definition['format'] = str_replace("\n%addressLine1", '', $definition['format']);
      $definition['format'] .= "\n%addressLine1";
    }

    $key = array_search('addressLine1', $definition['required_fields']);
    unset($definition['required_fields'][$key]);
    $definition['required_fields'][] = 'addressLine1';
    $definition['required_fields'] = array_values($definition['required_fields']);

    $event->setDefinition($definition);
  }

}
