<?php

namespace App\Exceptions;

use Exception;

trait CSPExceptions
{
    public function domainNotAllowed($message = 'Domain not allowed', $code = 401, Exception $previous = null)
    {
        return new Exception($message, $code, $previous);
    }
    public function reportNotSaved($message = 'CSP report not saved', $code = 400, Exception $previous = null)
    {
        return new Exception($message, $code, $previous);
    }
}

class CSP extends Exception
{
    use CSPExceptions;
}
