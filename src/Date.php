<?php

require_once 'DateValidator.php';

/**
 * Class Date
 */
class Date
{
    static private $SUNDAY = 7;
    static private $SATURDAY = 6;
    static private $WEEKEND = [6, 7];

    /**
     * @var string
     */
    private $date;

    /**
     * Date constructor.
     *
     * @param string $date
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $date)
    {
        if (!(new DateValidator($date))->isValid()) {
            throw new InvalidArgumentException('Invalid date');
        }
        $this->date = $date;
    }

    /**
     * @return bool
     */
    public function isWeekend(): bool
    {
        return in_array(
            date('N', strtotime($this->date)),
            self::$WEEKEND
        );
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->date;
    }
}
