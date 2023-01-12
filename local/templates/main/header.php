<?php

CJSCore::Init(['fx', 'ajax']);
?>


<!DOCTYPE html>
<html lang="<?= LANGUAGE_ID ?>">
<head>
  <meta charset="<?= SITE_CHARSET ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <?php
  //TODO : Asset::getInstance->addString()
  ?>
  <link rel="shortcut icon"
        href="<?= SITE_TEMPLATE_ASSETS ?>img/favicons/favicon.svg"
        type="image/svg+xml">
  <link rel="shortcut icon"
        href="<?= SITE_TEMPLATE_ASSETS ?>img/favicons/favicon.webp"
        type="image/webp">
  <link rel="shortcut icon"
        href="<?= SITE_TEMPLATE_ASSETS ?>img/favicons/favicon.png"
        type="image/x-icon">
  <link rel="preconnect" href="https://fonts.googleapis.com">

</head>
<body>
<div id="panel">
  <?php
  $APPLICATION->ShowHead();
  $APPLICATION->ShowPanel();
  ?>
</div>


