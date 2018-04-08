<?php

require_once 'Date.php';

/**
 * Class Image
 */
class Image
{
    private $image;

    public function __construct(string $image)
    {
        $this->image = $image;
    }

    /**
     * @return bool
     */
    public function isFromSecurityCamera()
    {
        $startsWithA = substr($this->image, 0, 1) === 'A';
        $hasValidLength = strlen($this->image) === 19;
        $extensionIsJPG = substr($this->image, -4) === '.jpg';
        return $startsWithA && $hasValidLength && $extensionIsJPG;
    }

    /**
     * @param string $timestamp
     *
     * @return bool
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function takenBefore(string $timestamp): bool
    {
        self::assertTimestamp($timestamp);
        return self::getTimestamp() < $timestamp;
    }

    /**
     * @param string $timestamp
     *
     * @return bool
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function takenAfter(string $timestamp): bool
    {
        self::assertTimestamp($timestamp);
        return !self::takenBefore($timestamp);
    }

    /**
     * @return Date
     * @throws Exception
     */
    public function getDate(): Date
    {
        if (!self::isFromSecurityCamera()) {
            throw new Exception('Not Implemented for this image');
        }
        $date = 20 . substr($this->image, 1, 6);
        return new Date($date);
    }

    /**
     * @param string $timestamp
     *
     * @throws InvalidArgumentException
     */
    private function assertTimestamp(string $timestamp)
    {
        if (strlen($timestamp) !== 12) {
            throw new InvalidArgumentException('Invalid timestamp given');
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    private function getTimestamp(): string
    {
        if (!self::isFromSecurityCamera()) {
            throw new Exception('Not Implemented for this image');
        }
        $timestamp = substr($this->image, 1, -4);
        $timestamp = substr($timestamp, 0, -2);
        return $timestamp;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->image;
    }
}