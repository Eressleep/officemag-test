<?php

use Bitrix\Main\Loader;

Loader::registerAutoLoadClasses(
    'officemag.module',
    [
        '\Officemag\Module\Entity\СuponsUsersTable' => 'lib/entity/СuponsUsers.php',
    ]
);
