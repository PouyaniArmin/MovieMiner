<?php
<<<<<<< HEAD

=======
>>>>>>> feature/routing
namespace App\Utility;

use Exception;

class ErrorHandler
{
    public static function errorMessage(string $message, int $code = 0): Exception
    {
      return throw new Exception($message, $code);
    }
}
