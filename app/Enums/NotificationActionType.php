<?php

namespace App\Enums;


abstract class NotificationActionType extends BasicEnum
{
    const ORDER_CREATED = 'order_created';
    const ORDER_STATUS = 'order_status';
    const REVIEW_USER = 'review_user';
    const REVIEW_GOODS = 'review_goods';
    const REMINDER = 'reminder';
    const SALE = 'sale';
}