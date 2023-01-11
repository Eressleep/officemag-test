<?php

namespace Officemag\Module\Entity;

use Bitrix\Main\Entity;
use Bitrix\Main\Entity\DatetimeField;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SystemException;

Loc::loadMessages(__FILE__);

class СuponsUsersTable extends Entity\DataManager
{

    public static function getTableName(): string
    {
        return 'coupons_users';
    }

    public static function getUfId(): string
    {
        return 'COUPONS_USERS';
    }

    public static function getMap(): array
    {
        return [
            new Entity\IntegerField(
                'ID',
                [
                    'primary'      => true,
                    'autocomplete' => true,
                    'title'        => Loc::getMessage('OFFICEMAG_COUPONS_USERS_ID'),
                ]
            ),
            new Entity\StringField(
                'COUPON',
                [
                    'required' => true,
                    'title'    => Loc::getMessage('OFFICEMAG_COUPONS_USERS_COUPON'),
                ]
            ),

            new Entity\IntegerField(
                'USER_ID',
                [
                    'required' => true,
                    'title'    => Loc::getMessage('OFFICEMAG_COUPONS_USERS_USER_ID'),
                ]
            ),
            new DatetimeField(
                'PUBLISH_DATE',
                [
                    'required' => true,
                    'title'    => Loc::getMessage('OFFICEMAG_COUPONS_USERS_PUBLISH_DATE'),
                ]
            )
        ];
    }
}
