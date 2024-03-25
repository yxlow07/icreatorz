<?php

namespace app\Exceptions;

use core\Exceptions\BaseException;

class UnderWorkException extends BaseException
{
    protected $message = 'This page is under works! Stay tuned';
    protected $code = 302;
}