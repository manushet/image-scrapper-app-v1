<?php

declare(strict_types=1);

namespace App\Exception;

use \Exception;

class UnreachableResourceContentException extends Exception
{
    protected $message = "Failed to fetch resource content data.";
}