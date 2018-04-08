<?php

/**
 * Class Validator
 */
abstract class Validator
{
    /**
     * Validator constructor.
     *
     * @param string $input
     */
    abstract function __construct(string $input);
}