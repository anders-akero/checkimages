<?php

require_once 'Authenticate.php';

/**
 * Class Auth
 */
class Auth extends Authenticate
{
    /**
     * @return string
     */
    public function getToken(): string
    {
        return parent::AUTH_KEY;
    }

}