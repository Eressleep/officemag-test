<?php

use Bitrix\Main\Localization\Loc; ?>

<div class="cupon-wraper">
    <button id="js-getCupon"><?= Loc::getMessage('GET_CODE') ?></button>
    <div class="hide" id="js-showCupon"></div>
    <div class="hide" id="js-showDiscount"></div>
    <form id="js-checkCupon" action="">
        <input name="code" type="text">
        <button type="submit"><?= Loc::getMessage('CHECK_CODE') ?></button>
        <div id="js-statusCupon"></div>
    </form>
</div>
