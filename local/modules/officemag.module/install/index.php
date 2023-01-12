<?php

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\LoaderException;
use Bitrix\Main\SystemException;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Officemag\Module\Entity\СuponsUsersTable as СuponsUsers;

class officemag_module extends CModule
{
    /**
     * Prepare module for use.
     */
    public function __construct()
    {
        if (file_exists(__DIR__ . '/version.php')) {
            $arModuleVersion = [];
            include_once(__DIR__ . '/version.php');

            if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
                $this->MODULE_VERSION      = $arModuleVersion['VERSION'];
                $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
            }

            $this->MODULE_ID = str_replace('_', '.', __CLASS__);

            $this->MODULE_NAME        = Loc::getMessage('OFFICEMAG_NAME');
            $this->MODULE_DESCRIPTION = Loc::getMessage('OFFICEMAG_DESCRIPTION');
            $this->PARTNER_NAME       = Loc::getMessage('OFFICEMAG_PARTNER_NAME');
            $this->PARTNER_URI        = Loc::getMessage('OFFICEMAG_PARTNER_URI');

            $this->MODULE_SORT                   = 1;
            $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = 'Y';
            $this->MODULE_GROUP_RIGHTS           = 'Y';

        }
    }

    /**
     * Install all module packages.
     *
     * @return bool
     * @throws ArgumentException
     * @throws LoaderException
     * @throws SystemException
     */
    public function DoInstall(): bool
    {
        global $APPLICATION;


        if (CheckVersion(ModuleManager::getVersion('main'), '14.00.00')) {
            ModuleManager::registerModule($this->MODULE_ID);
            if (Loader::includeModule($this->MODULE_ID) ) {
                //TODO : Добавить обработку
                $this->InstallDB();


                $APPLICATION->IncludeAdminFile(
                    Loc::getMessage('OFFICEMAG_INSTALL_TITLE') . Loc::getMessage('OFFICEMAG_NAME') . '.',
                    __DIR__ . '/step.php'
                );
            }
        } else {
            $APPLICATION->ThrowException(
                Loc::getMessage('OFFICEMAG_INSTALL_ERROR_VERSION')
            );
        }



        return false;
    }

    /**
     * Install additional table for module.
     *
     * @throws LoaderException
     * @throws ArgumentException
     * @throws SystemException
     */
    public function InstallDB() : bool
    {
        Loader::includeModule($this->MODULE_ID);
        if (!Application::getConnection()->isTableExists(

            Base::getInstance(СuponsUsers::getEntity()->getDataClass())->getDBTableName()
        )) {
            СuponsUsers::getEntity()->createDbTable();
            return true;
        }
        return false;
    }


    /**
     * Unistall all module packages.
     *
     * @return bool
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws LoaderException
     * @throws SqlQueryException
     * @throws SystemException
     */
    public function DoUninstall() : bool
    {
        global $APPLICATION;

        if (Loader::includeModule($this->MODULE_ID)) {

            //TODO : Добавить обработку
            $this->UnInstallDB();
            ModuleManager::unRegisterModule($this->MODULE_ID);
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage('OFFICEMAG_UNINSTALL_TITLE') . Loc::getMessage('OFFICEMAG_NAME') .'.',
                __DIR__ . '/unset.php'
            );


            return true;
        }
        return false;
    }

    /**
     * Unistall additional table for module.
     *
     * @throws ArgumentNullException
     * @throws ArgumentException
     * @throws SqlQueryException
     * @throws SystemException
     */
    public function UnInstallDB(): bool
    {
        if (Application::getConnection()->isTableExists(
            Base::getInstance(СuponsUsers::getEntity()->getDataClass())->getDBTableName()
        )) {
            $connection = Application::getInstance()->getConnection();
            $connection->dropTable(СuponsUsers::getTableName());
            Option::delete($this->MODULE_ID);
            return true;
        }
        return false;

    }


}
