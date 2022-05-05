<?php
defined('_JEXEC') or die();

define('_JSHOP_SERVICE_ID_LABEL', 'Номер услуги');
define('_JSHOP_SERVICE_ID_COMMENT', 'Генерирутся в панели express-pay.by');
define('_JSHOP_TOKEN_LABEL', 'Токен');
define('_JSHOP_TOKEN_COMMENT', 'Генерирутся в панели express-pay.by');
define('_JSHOP_HANDLER_LABEL', 'Адрес для уведомлений');
define('_JSHOP_SIGN_INVOICES_LABEL', 'Использовать цифровую подпись для API');
define('_JSHOP_SIGN_COMMENT', 'Параметр проверки запросов с использование цифровой подписи');
define('_JSHOP_SIGN_NOTIFY_LABEL', 'Использовать цифровую подпись для уведомлений');
define('_JSHOP_STATUS_END', 'Статус при оплате заказа');
define('_JSHOP_STATUS_PENDING', 'Статус заказа ожидающего оплаты');
define('_JSHOP_STATUS_FAILED', 'Статус при отмене заказа');
define('_JSHOP_SECRET_KEY_LABEL', 'Секретное слово для подписи счетов');
define('_JSHOP_SECRET_KEY_COMMENT', 'Секретного слово, которое известно только серверу и клиенту. Используется для формирования цифровой подписи. Задается в панели express-pay.by');
define('_JSHOP_SECRET_KEY_NOTIFY_LABEL', 'Секретное слово для подписи уведомлений');
define('_JSHOP_ADDRESS_EDITABLE_LABEL', 'Разрешено изменять адрес плательщика');
define('_JSHOP_ADDRESS_EDITABLE_COMMENT', 'Разрешается при оплате счета изменять адрес плательщика');
define('_JSHOP_NAME_EDITABLE_LABEL', 'Разрешено изменять ФИО плательщика');
define('_JSHOP_NAME_EDITABLE_COMMENT', 'Разрешается при оплате счета изменять ФИО плательщика');
define('_JSHOP_AMOUNT_EDITABLE_LABEL', 'Разрешено изменять сумму оплаты');
define('_JSHOP_AMOUNT_EDITABLE_COMMENT', 'Разрешается при оплате счета изменять сумму платежа');
define('_JSHOP_TEST_MODE_LABEL', 'Использовать тестовый режим');
define('_JSHOP_URL_API_LABEL', 'Адрес API');
define('_JSHOP_URL_SANDBOX_API_LABEL', 'Адрес тестового API');
define('_JSHOP_SETTINGS_MODULE_LABEL', 'Настройки модуля');
define('_JSHOP_MESSAGE_SUCCESS_LABEL', 'Сообщение при успешном заказе');
define('_JSHOP_TEXT_VERSION', 'Версия ');
define('_JSHOP_HEADING_TITLE_ERROR', 'Ошибка выставления счета в системе ЕРИП');
define('_JSHOP_HEADING_TITLE', 'Счет добавлен в систему ЕРИП для оплаты');
define('_JSHOP_TEXT_MESSAGE_ERROR', 'При выполнении запроса произошла непредвиденная ошибка. Пожалуйста, повторите запрос позже или обратитесь в службу технической поддержки по адресу info@express-pay.by');
define('_JSHOP_TEST_MODE_LABEL_FRONT', 'Тестовый режим: ');
define('_JSHOP_TEXT_MESSAGE', 'Ваш номер заказа: ');
define('_JSHOP_SEND_NOTIFY_SUCCESS', 'Отправить уведомление об успешной оплате');
define('_JSHOP_SEND_NOTIFY_CANCEL', 'Отправить уведомление об отмене оплаты');
define('_JSHOP_TEXT_ABOUT', '«Экспресс Платежи: ЕРИП» - плагин для интеграции с сервисом «Экспресс Платежи» (express-pay.by) через API. 
<br/>Плагин позволяет выставить счет в системе ЕРИП, получить и обработать уведомление о платеже в системе ЕРИП.
<br/>Описание плагина доступно по адресу: <a target="blank" href="https://express-pay.by/extensions/joomshopping-3-4/erip">https://express-pay.by/extensions/joomshopping-3-4/erip</a>');
define('_JSHOP_MESSAGE_SUCCESS', 'Для оплаты заказа Вам необходимо перейти в раздел ЕРИП:

Интернет-магазины\Сервисы -> "Первая буква доменного имени интернет-магазина" -> "Доменное имя интернет-магазина"

Далее введите номер заказа "##order_id##" и нажмите "продолжить".

После поступления оплаты Ваш заказ поступит в обработку.');