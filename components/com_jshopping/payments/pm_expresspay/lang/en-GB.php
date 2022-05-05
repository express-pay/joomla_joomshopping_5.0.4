<?php
defined('_JEXEC') or die();

define('_JSHOP_TOKEN_LABEL', 'Token');
define('_JSHOP_TOKEN_COMMENT', 'Generate in panel express-pay.by');
define('_JSHOP_HANDLER_LABEL', 'URL for notification');
define('_JSHOP_SIGN_INVOICES_LABEL', 'Use digital signature for API');
define('_JSHOP_SIGN_COMMENT', 'Parameter validation requests the use of digital signatures');
define('_JSHOP_SIGN_NOTIFY_LABEL', 'Use digital signature for notification');
define('_JSHOP_STATUS_END', 'Order Status for successful transactions');
define('_JSHOP_STATUS_PENDING', 'Order Status for Pending Payments');
define('_JSHOP_STATUS_FAILED', 'Order Status for failed transaction');
define('_JSHOP_SECRET_KEY_LABEL', 'Secret word for signed invoices');
define('_JSHOP_SECRET_KEY_COMMENT', 'A secret word that is known only to the server and the client. Used to form the digital signature. Set in express-pay.by panel');
define('_JSHOP_SECRET_KEY_NOTIFY_LABEL', 'Secret word for signed notification');
define('_JSHOP_ADDRESS_EDITABLE_LABEL', 'Allow edit address payer');
define('_JSHOP_ADDRESS_EDITABLE_COMMENT', 'Allowed when paying bills to change the address of the payer');
define('_JSHOP_NAME_EDITABLE_LABEL', 'Allow edit full name payer');
define('_JSHOP_NAME_EDITABLE_COMMENT', 'Allowed when paying bills to change the name of the payer');
define('_JSHOP_AMOUNT_EDITABLE_LABEL', 'Allow edit amount payment');
define('_JSHOP_AMOUNT_EDITABLE_COMMENT', 'Allowed when paying bills to change the payment amount');
define('_JSHOP_TEST_MODE_LABEL', 'Use test mode');
define('_JSHOP_URL_API_LABEL', 'URL API');
define('_JSHOP_URL_SANDBOX_API_LABEL', 'URL for sandbox API');
define('_JSHOP_SETTINGS_MODULE_LABEL', 'Settings module');
define('_JSHOP_MESSAGE_SUCCESS_LABEL', 'Message in a successful order');
define('_JSHOP_TEXT_VERSION', 'Version ');
define('_JSHOP_HEADING_TITLE_ERROR', 'An error in the payment method');
define('_JSHOP_HEADING_TITLE', 'The score has been added to the payment method');
define('_JSHOP_TEXT_MESSAGE_ERROR', 'An unexpected error occurred while executing the query. Please try again later or contact Technical Support at info@express-pay.by');
define('_JSHOP_TEST_MODE', 'Test mode: ');
define('_JSHOP_TEXT_MESSAGE', 'Your order number: ');
define('_JSHOP_SEND_NOTIFY_SUCCESS', 'Send notification for success payment');
define('_JSHOP_SEND_NOTIFY_CANCEL', 'Send notification for cancel payment');
define('_JSHOP_TEXT_ABOUT', '«Express Payments» - plug-in for integration with the service «Express Payments» (express-pay.by) through the API.
<br/>plugin allows you to invoice in system to receive and process the payment notice in the system.
<br/>Description plugin is available at: <a target="blank" href="https://express-pay.by/extensions/joomshopping-3-4/erip">https://express-pay.by/extensions/joomshopping-3-4/erip</a>');
define('_JSHOP_MESSAGE_SUCCESS', 'To order a payment, you must go to the section:

Online shops \ Services -> "The first letter of a domain name online store" -> "The domain name is the online store"

Next, enter the order number "##order_id##" and click "continue".

After payment is received your order will go on treatment.');