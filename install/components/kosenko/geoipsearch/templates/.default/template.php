<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;
use Bitrix\Main\Page\Asset;

CJSCore::Init(['ajax', 'masked_input']);

Extension::load("ui.buttons");
Extension::load("ui.alerts");
Extension::load("ui.dialogs.messagebox");
Extension::load("ui.forms");
Extension::load('ui.bootstrap4');

$messages = Loc::loadLanguageFile(__FILE__);

Asset::getInstance()->addJs('https://api-maps.yandex.ru/2.1/?apikey=e95f8762-4d63-49c0-94d2-bb06804ddd4e&lang=ru_RU');
?>

<div class="container gy-2">
    <div class="row ">
        <div class="col-md-4">
            <div class="ui-ctl ui-ctl-textbox ui-ctl-inline">
                <input type="text" class="ui-ctl-element" id="ipadress" placeholder="xxx.xxx.xxx.xxx">
            </div>
        </div>
        <div class="col-md-2">
            <button class="ui-btn" id="geoipsearch-button">Поиск</button>
        </div>
    </div>
</div>

<div class="container gy-4 result-body" style="display:none">
    <div class="row">
        <div class="col-md-7">
            <table class="result-table table">
            <tbody>
                <tr>
                    <td scope="row">IP адрес</td>
                    <td class="result-ip"></td>
                </tr>
                <tr>
                    <td scope="row">Координаты</td>
                    <td class="result-coords"></td>
                </tr>
                <tr>
                    <td scope="row">Страна</td>
                    <td class="result-country"></td>
                </tr>
                <tr>
                    <td scope="row">Регион</td>
                    <td class="result-region"></td>
                </tr>
                <tr>
                    <td scope="row">Город</td>
                    <td class="result-city"></td>
                </tr>
                </tbody>
            </table>
        </div>

    </div>
    <div class="row" style="height: 400px; width: 100%;">
        <div class="col-md-12">
            <div id="map" style="width: 100%; height: 100%;">
            </div>
    </div>
</div>

<script>
    BX.message(<?= CUtil::PhpToJSObject($messages) ?>);
</script>


