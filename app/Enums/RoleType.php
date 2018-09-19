<?php

namespace App\Enums;


abstract class RoleType extends BasicEnum
{
    const ADMIN = 'admin';
    const PROVIDER = 'provider';
    const CUSTOMER = 'customer';
}