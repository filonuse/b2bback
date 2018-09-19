<?php

namespace App\Services;


use App\Enums\RoleType;
use App\Enums\SettingType;
use App\Models\User;

class SettingService
{
    /**
     * @param User $user
     * @return bool
     */
    public static function saveToDefault(User $user)
    {
        $repository = self::getRepository();
        $data = self::getDefaultData($user);

        foreach ($data as $item) {
            $repository->create($item);
        }
    }

    /**
     * @return \App\Repositories\SettingRepository
     */
    protected static function getRepository()
    {
        return app('App\Repositories\SettingRepository');
    }

    /**
     * @param User $user
     * @return array
     */
    protected static function getDefaultData(User $user)
    {
        switch ($user->roleName()) {
            case RoleType::CUSTOMER:
                return [
                    [
                        'user_id' => $user->id,
                        'name'    => SettingType::NOTIFY_STATUS_UPDATED,
                        'value'   => 'on',
                    ],
                    [
                        'user_id' => $user->id,
                        'name'    => SettingType::NOTIFY_SALE,
                        'value'   => 'on',
                    ],
                    [
                        'user_id' => $user->id,
                        'name'    => SettingType::NOTIFY_REVIEW_CREATED,
                        'value'   => 'on',
                    ],
                ];

            case RoleType::PROVIDER:
                return [
                    [
                        'user_id' => $user->id,
                        'name'    => SettingType::NOTIFY_REVIEW_CREATED,
                        'value'   => 'on',
                    ],
                    [
                        'user_id' => $user->id,
                        'name'    => SettingType::NOTIFY_ORDER_CREATED,
                        'value'   => 'on',
                    ],
                ];
        }
    }
}