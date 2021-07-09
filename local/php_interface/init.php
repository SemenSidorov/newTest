<?
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", "OnBeforeIBlockElementUpdateHandler");
AddEventHandler("iblock", "OnBeforeIBlockElementDelete", "OnBeforeIBlockElementDeleteHandler");

function OnBeforeIBlockElementUpdateHandler($arFields)
{
    if($arFields["IBLOCK_ID"] == CATALOG_ID && $arFields["DATE_CREATE"] + 60*60*24*7 >= time()){
        $APPLICATION->ThrowException("Товар " . $arFields["NAME"] . " был создан менее одной недели назад и не может быть изменен.");
        return false;
    }
}

function OnBeforeIBlockElementDeleteHandler($arFields)
{
    if($arFields["IBLOCK_ID"] == CATALOG_ID && $arFields["SHOW_COUNTER"] >= 10000){
        $APPLICATION->ThrowException("Нельзя удалить данный товар, так как он очень популярный на сайте");
        CEvent::Send("DELETE_POPELEM", 's1', [
            "LOGIN" => $USER->GetLogin(),
            "ITEM_NAME" => $arFields["NAME"],
            "SHOW_COUNTER" => $arFields["SHOW_COUNTER"]
        ]);// Вызывает почтовое событие DELETE_POPELEM
        // Текст шаблона почтового события DELETE_POPELEM:
        // Пользователь #LOGIN# пытается удалить популярный товар #ITEM_NAME#, у которого #SHOW_COUNTER# показов на сайте.
        // Шаблон отправляется на #DEFAULT_EMAIL#
        return false;
    }
}
?>