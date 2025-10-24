<?php
namespace Twisto;

use Exception;

class Error extends Exception
{
    public mixed $data;

    public function __construct( string $message = '', mixed $data = null, $code = 0, ?Exception $previous = null)
    {
        $this->data = $data;
        parent::__construct($message, $code, $previous);
    }

}