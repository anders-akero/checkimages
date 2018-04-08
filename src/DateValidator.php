<?php

require_once 'Validator.php';

/**
 * Class DateValidator
 */
class DateValidator extends Validator
{
    /**
     * @var string
     */
    private $date;

    /**
     * Validator constructor.
     *
     * @param string $date
     */
    public function __construct(string $date)
    {
        $this->date = $date;
    }

    function isValid(): bool
    {
        if (preg_match("/^(\d{4})(\d{2})(\d{2})$/", $this->date, $matches)) {
            if (checkdate($matches[2], $matches[3], $matches[1])) {
                return true;
            }
        }
        return false;
    }
}