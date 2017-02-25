<?php

namespace Wideas\SMS\Kavenegar\Exceptions;

class HttpException extends BaseRuntimeException
{
	public function getName()
    {
        return 'HttpException';
    }	
}