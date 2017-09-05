<?php

namespace Wideas\SMS\Kavenegar\Exceptions;

class ApiException extends BaseRuntimeException
{
    public function getName()
    {
        return 'ApiException';
    }
}