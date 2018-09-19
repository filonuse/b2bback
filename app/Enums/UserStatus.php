<?php

namespace App\Enums;


abstract class UserStatus extends BasicEnum
{
    const BANNED = 'banned';
    const DELETED = 'deleted';
}