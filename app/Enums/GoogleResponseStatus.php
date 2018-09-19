<?php

namespace App\Enums;


abstract class GoogleResponseStatus extends BasicEnum
{
    const SUCCESS = 'OK';
    const ZERO_RESULTS = 'ZERO_RESULTS ';
    const OVER_QUERY_LIMIT = 'ZERO_RESULTS ';
    const REQUEST_DENIED = 'ZERO_RESULTS ';
    const INVALID_REQUEST = 'ZERO_RESULTS ';
}