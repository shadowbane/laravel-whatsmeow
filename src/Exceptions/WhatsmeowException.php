<?php

namespace Shadowbane\Whatsmeow\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

/**
 * Class WhatsmeowException.
 *
 * @package Shadowbane\Whatsmeow\Exceptions
 */
class WhatsmeowException extends Exception
{
    /**
     * Thrown when Whatsmeow API Token is not provided / empty.
     *
     * @return static
     */
    #[Pure]
 public static function tokenIsEmpty(): static
 {
     return new static('Whatsmeow API Token is empty');
 }

    /**
     * Thrown when Whatsmeow API Token is not provided / empty.
     *
     * @return static
     */
    #[Pure]
 public static function urlIsEmpty(): static
 {
     return new static('Whatsmeow API Endpoint url is empty');
 }

    /**
     * Thrown when WhatsApp destination number is not provided / empty.
     *
     * @return static
     */
    #[Pure]
 public static function destinationIsEmpty(): static
 {
     return new static('Destination WhatsApp Number is invalid or empty');
 }

    /**
     * Thrown when Wablas API Token is not provided / empty.
     *
     * @return static
     */
    #[Pure]
 public static function invalidMessageType(): static
 {
     return new static('Invalid message type.');
 }
}
