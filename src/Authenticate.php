<?php

require_once 'Validator.php';
require_once 'UnauthorizedAccessException.php';

/**
 * Class Auth
 */
class Authenticate extends Validator
{
    static protected $AUTH_KEY = 'princessessee';
    /**
     * @var string
     */
    private $token;

    /**
     * Auth constructor.
     *
     * @param string $token
     *
     * @throws UnauthorizedAccessException
     */
    public function __construct(string $token)
    {
        self::assertValidToken($token);
        $this->token = $token;
    }

    /**
     * @param string $token
     *
     * @throws UnauthorizedAccessException
     */
    private function assertValidToken(string $token)
    {
        if ($token !== self::$AUTH_KEY) {
            throw new UnauthorizedAccessException('Invalid token');
        }
    }
}
