<?php

namespace App\Enums;


abstract class OrderStatus extends BasicEnum
{
    const PENDING = 'pending';
    const PROCESSED = 'processed';
    const SHIPPED = 'shipped';
    const ACCEPTED_CUSTOMER = 'accepted_customer';
    const CANCELED = 'canceled';
}