<?php

namespace Nurdin\Djawara\Helper;

use Exception;

class ErrorHelper
{
    public static function errors(Exception $e)
    {
        http_response_code($e->getCode());
        echo json_encode([
            'errors' => $e->getMessage()
        ]);
        exit();
    }
}
