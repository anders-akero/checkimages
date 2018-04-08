<?php

require_once 'DateValidator.php';

/**
 * Class Date
 */
class Date
{
    private const SUNDAY = 7;
    private const SATURDAY = 6;
    private const WEEKEND = [self::SATURDAY, self::SUNDAY];

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
            self::WEEKEND
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