<?php

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Errorable;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Officemag\Module\Entity\СuponsUsersTable as СuponsUsers;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Localization\Loc;

class Cupons extends CBitrixComponent implements Controllerable, Errorable
{

    /**
     * Module name.
     *
     * @var string
     */
    static string $muduleName = 'officemag.module';

    /**
     * Discount down bound.
     *
     * @var int
     */

    static int $randDown = 1;
    /**
     * Discount upper bound.
     *
     * @var int
     */
    static int $randUp   = 50;

    /**
     * Collection error.
     */
    protected object $errorCollection;

    /**
     * Validate data.
     *
     * @param array $arParams
     *
     * @return array
     */
    public function onPrepareComponentParams(array $arParams) : array
    {
        $this->errorCollection = new ErrorCollection();
        return $arParams;
    }

    /**
     * Point of entry.
     *
     * @return void
     * @throws LoaderException
     */
    public function executeComponent() : void
    {
        if(CurrentUser::get()->getId() and  Loader::includeModule(self::$muduleName)) {
            $this->includeComponentTemplate();
        }
    }
    /**
     * Setting up routes.
     *
     * @return ActionFilter\HttpMethod[][][]
     */
    public function configureActions() : array
    {
        return [
            'getCupon'   => [
                'prefilters' => [
                    new ActionFilter\HttpMethod(
                        [
                            ActionFilter\HttpMethod::METHOD_GET,
                            ActionFilter\HttpMethod::METHOD_POST,
                        ]
                    ),
                ],
            ],
            'checkCupon' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod(
                        [
                            ActionFilter\HttpMethod::METHOD_GET,
                            ActionFilter\HttpMethod::METHOD_POST,
                        ]
                    ),
                ],
            ],
        ];
    }


    /**
     * @throws Exception
     */
    public static function generateCode($user): array
    {
        $publish_date = DateTime::createFromTimestamp(time());
        $cupon = md5($user->getId().$publish_date->toString());
        $discount = rand(self::$randDown, self::$randUp);
        try {
            $status = СuponsUsers::add(
                [
                    'COUPON'       => $cupon,
                    'USER_ID'      => $user->getId(),
                    'PUBLISH_DATE' => $publish_date,
                    'DISCOUNT'     => $discount,
                ]
            );
        } catch (Exception $e) {
            throw new Exception('Failed to add entry.');
        }

        if($status->isSuccess())
        {
            return self::returnAnswer($cupon, $discount);
        }else{
            throw new Exception('Failed to add entry.');
        }
    }

    /**
     * Returning answer.
     *
     * @param string|null $code
     * @param string|null $discount
     * @param bool        $status
     *
     * @return array
     */
    public function returnAnswer(?string $code, ?string $discount, bool $status = true): array
    {
        return [
            'code'     => Loc::getMessage('CUPON').$code,
            'discount' => Loc::getMessage('DISCOUNT').$discount,
            'status'   => $status,
        ];
    }

    /**
     * Check lifetime cupon.
     *
     * @param        $user
     * @param int    $hour
     * @param string $coupon
     *
     * @return array
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function checkTime($user, int $hour = 1, string $coupon = '') : array
    {
        $filter = [
            'USER_ID' => $user->getId(),
            '>=PUBLISH_DATE' => DateTime::createFromTimestamp(time())->add("-$hour hours"),
        ];
        if(strlen($coupon))
        {
            $filter['=COUPON'] = $coupon;
        }
        $data = СuponsUsers::getList(
            [
                'filter' => $filter,
                'order'  => [
                    'PUBLISH_DATE' => 'DESC',
                ],
            ]
        )->fetch();
        if(is_bool($data))
        {
            return (new Cupons)->returnAnswer($data['COUPON'], $data['DISCOUNT'], false);
        }else{
            return (new Cupons)->returnAnswer($data['COUPON'], $data['DISCOUNT']);
        }

    }

    /**
     * Generate cupon for user;
     *
     * @return array
     * @throws LoaderException
     */
    public function getCuponAction()
    {
        try {
            Loader::includeModule(self::$muduleName);
            $user = CurrentUser::get();
            $status = self::checkTime($user);
            if($status['status'])
            {
                return $status;
            }else {
                return self::generateCode($user);
            }
        }catch (Exception $exception) {
            $this->errorCollection->setError(new Bitrix\Main\Error($exception->getMessage(), 'Bad data'));
        }
    }

    public function checkCuponAction($post){
        Loader::includeModule(self::$muduleName);
        $code = '';
        $user = CurrentUser::get();
        foreach ($post as $item)
        {
            if($item['name'] == 'code')
            {
                $code = $item['value'];
            }
        }
        $data = self::checkTime($user, 3, $code);
        if(!strlen($code) or !$data['status'])
        {
           return self::returnAnswer('', Loc::getMessage('UNAVAILIBLE'), false);
        }else{

            return $data;
        }
    }

    /**
     * Get error.
     *
     * @return array|\Bitrix\Main\Error[]
     */
    public function getErrors(): array
    {
        return $this->errorCollection->toArray();
    }

    /**
     * Get error.
     *
     * @param $code
     *
     * @return \Bitrix\Main\Error
     */
    public function getErrorByCode($code): \Bitrix\Main\Error
    {
        return $this->errorCollection->getErrorByCode($code);
    }
}
