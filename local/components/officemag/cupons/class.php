<?php

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Errorable;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Loader;
use Officemag\Module\Entity\СuponsUsersTable as СuponsUsers;
use Bitrix\Main\Type;
use Bitrix\Main\Type\DateTime;

class Cupons extends CBitrixComponent implements Controllerable, Errorable
{

    static int $randDown = 1;
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
     */
    public function executeComponent() : void
    {
        $this->includeComponentTemplate();
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
     * Generate cupon for user;
     *
     * @return array
     * @throws \Bitrix\Main\LoaderException
     */
    public function getCuponAction(){
        try {
            Loader::includeModule('officemag.module');
            $user = CurrentUser::get();
            $publish_date = DateTime::createFromTimestamp(time());
            $cupon = md5($user->getId().$publish_date->toString());
            $discount = rand(self::$randDown, self::$randUp);
            $status = СuponsUsers::add(
                [
                    'COUPON'       => $cupon,
                    'USER_ID'      => $user->getId(),
                    'PUBLISH_DATE' => $publish_date,
                    'DISCOUNT'     => $discount,
                ]
            );

            if($status->isSuccess())
            {
                return [
                    'code'     => 'Купон : '.$cupon,
                    'discount' => 'Скидка : '.$discount
                ];
            }else{

                throw new Exception('Failed to add entry.');
            }
        }catch (Exception $exception)
        {
            $this->errorCollection->setError(new Bitrix\Main\Error($exception->getMessage(), 'Bad data'));
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
     */
    public function getErrorByCode($code): \Bitrix\Main\Error
    {
        return $this->errorCollection->getErrorByCode($code);
    }
}
