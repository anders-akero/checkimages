<?php

/**
 * Class Response
 */
class Response
{
    static public $HTTP_OK = 200;
    static public $HTTP_UNAUTHORIZED = 401;
    static public $HTTP_FORBIDDEN = 403;

    /**
     * Response constructor.
     *
     * @param mixed $content
     * @param int   $status
     */
    public function __construct($content = '', int $status = null)
    {
        $status = self::$HTTP_OK;
        switch ($status) {
            case self::$HTTP_OK:
                $statusText = 'OK';
                break;
            case self::$HTTP_UNAUTHORIZED:
                $statusText = 'Unauthorized';
                break;
            case self::$HTTP_FORBIDDEN:
                $statusText = 'Forbidden';
                break;
            default:
                throw new InvalidArgumentException('Not Implemented');
                break;
        }
        header('WWW-Authenticate: Basic realm="Security Camera", charset="UTF-8"');
        header('HTTP/1.1 ' . $status . ' ' . $statusText);
        die($content);
    }
}
