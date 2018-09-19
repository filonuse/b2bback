<?php

namespace App\Enums;


abstract class SettingType extends BasicEnum
{
    const NOTIFY_ORDER_CREATED = 'notify_order_created';
    const NOTIFY_STATUS_UPDATED = 'notify_order_status_updated';
    const NOTIFY_REVIEW_CREATED = 'notify_review_created';
    const NOTIFY_SALE = 'notify_sale';
}