<?php

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
?>
<?php

$APPLICATION->IncludeComponent(
    'officemag:cupons',
    'main',
    []
);

?>
<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
?>
