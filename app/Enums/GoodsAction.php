<?php

namespace App\Enums;


abstract class GoodsAction extends BasicEnum
{
    const RESERVE = 'reserve';
    const WRITE_OFF = 'write_off';
    const REVERT = 'revert';
}