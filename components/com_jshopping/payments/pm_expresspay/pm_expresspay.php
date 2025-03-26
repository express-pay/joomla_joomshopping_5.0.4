<?php
defined('_JEXEC') or die('Restricted access');
define("EXPRESSPAY_VERSION", "1.0");

use Joomla\CMS\Factory;

class pm_expresspay extends PaymentRoot
{

	function loadLanguageFile()
	{
		$lang = Factory::getApplication()->getLanguage();
		$langtag = $lang->getTag();

		if (file_exists(JPATH_ROOT . '/components/com_jshopping/payments/pm_expresspay/lang/' . $langtag . '.php'))
			require_once(JPATH_ROOT . '/components/com_jshopping/payments/pm_expresspay/lang/' . $langtag . '.php');
		else
			require_once(JPATH_ROOT . '/components/com_jshopping/payments/pm_expresspay/lang/ru-RU.php');

		return substr($langtag, 0, 2);
	}

	function showAdminFormParams($params)
	{
		$array_params = array('expresspay_service_id', 'expresspay_token', 'expresspay_sign_invoices', 'expresspay_secret_key', 'expresspay_sign_notify', 'expresspay_secret_key_notify', 'expresspay_name_editable', 'expresspay_address_editable', 'expresspay_amount_editable', 'expresspay_test_mode', 'expresspay_url_api', 'expresspay_url_sandbox_api', 'transaction_end_status', 'transaction_pending_status', 'transaction_failed_status', 'expresspay_test_mode');

		$lang = $this->loadLanguageFile();

		foreach ($array_params as $key) {
			if (!isset($params[$key]) || (isset($params[$key]) && empty($params[$key])))
				switch ($key) {
					case 'expresspay_url_sandbox_api':
						$params[$key] = 'https://sandbox-api.express-pay.by';

						break;
					case 'expresspay_url_api':
						$params[$key] = 'https://api.express-pay.by';

						break;
					case 'expresspay_message_success':
						$params[$key] = _JSHOP_MESSAGE_SUCCESS;

						break;
					default:
						$params[$key] = '';

						break;
				}
		}

		$orders = JModelLegacy::getInstance('orders', 'JshoppingModel');

		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array($lang)))->from($db->quoteName('#__jshopping_payment_express_pay'));
		$db->setQuery($query);
		$message_success = $db->loadResult();

		include(dirname(__FILE__) . "/adminparamsform.php");
	}

	function showEndForm($pmconfigs, $order)
	{
		$this->log_info('showEndForm', 'Initialization request for add invoice');

		$pm_method = $this->getPmMethod();
		$secret_word = $pmconfigs['expresspay_secret_key'];

		$url = (isset($pmconfigs['expresspay_test_mode']) && $pmconfigs['expresspay_test_mode'] == 'on') ? $pmconfigs['expresspay_url_sandbox_api'] : $pmconfigs['expresspay_url_api'];
		$url .= "/v2/invoices";

		$currency = (date('y') > 16 || (date('y') >= 16 && date('n') >= 7)) ? '933' : '974';

		$request_params = array(
			"ServiceId" => $pmconfigs['expresspay_service_id'],
			"AccountNo" => $order->order_id,
			"Amount" => $order->order_total,
			"Currency" => $currency,
			"Surname" => $order->l_name,
			"FirstName" => $order->f_name,
			"City" => $order->city,
			"Info" => "Оплата заказа №".$order->order_id,
			"ReturnUrl" => JURI::root() . "index.php?option=com_jshopping&controller=checkout&task=step7&act=return&js_paymentclass=$pm_method->payment_class&order_id=$order->order_id",
			"FailUrl" => JURI::root() . "index.php?option=com_jshopping&controller=checkout&task=step7&act=cancel&js_paymentclass=$pm_method->payment_class&order_id=$order->order_id",
			"ReturnType" => "redirect",
			"IsNameEditable" => ((isset($pmconfigs['expresspay_name_editable']) && $pmconfigs['expresspay_name_editable'] == 'on') ? 1 : 0),
			"IsAddressEditable" => ((isset($pmconfigs['expresspay_address_editable']) && $pmconfigs['expresspay_address_editable'] == 'on') ? 1 : 0),
			"IsAmountEditable" => ((isset($pmconfigs['expresspay_amount_editable']) && $pmconfigs['expresspay_amount_editable'] == 'on') ? 1 : 0)
		);

		$token = $pmconfigs['expresspay_token'];

		$request_params['signature'] = $this->compute_signature_add_invoice($request_params, $secret_word, $token);

		$this->log_info('showEndForm', 'Send POST request; ORDER ID - ' . $order->order_number . '; URL - ' . $url . '; REQUEST - ' . print_r($request_params, 1));

?>
		<html>

		<head>
			<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		</head>

		<body>
			<form id="expresspaypaymentform" action=<?php echo $url ?> name="expresspaypaymentform" method="post">
				<?php
				foreach ($request_params as $name => $value) {
					echo '<input type="hidden" name="' . $name . '" value="' . $value . '">' . "\n\t\t\t\t";
				}
				?>
			</form>
			<?php echo \JText::_('JSHOP_REDIRECT_TO_PAYMENT_PAGE') ?>
			<script>
				document.getElementById('expresspaypaymentform').submit();
			</script>
		</body>

		</html>
<?php
		die;
	}

	function getUrlParams($pmconfigs)
	{
		$this->log_info('getUrlParams', 'Get notify from server; REQUEST METHOD - ' . $_SERVER['REQUEST_METHOD']);
		$params = array();
		$application = Factory::getApplication();
		$input = $application->getInput();

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$data = htmlspecialchars_decode($input->getString('Data'));

			try {
				if (!empty($data))
					$data = json_decode($data);

				$params['order_id'] = $data->AccountNo;
			} catch (Exception $e) {
				$this->log_error('getUrlParams', "Fail to parse the server response; RESPONSE - " . htmlspecialchars_decode($input->getString('Data')));

				$params['order_id'] = 0;
			}
		}

		$params['hash'] = "";
		$params['checkHash'] = 0;
		$params['checkReturnParams'] = 0;

		$this->log_info('getUrlParams', 'End (Get notify from server); REQUEST METHOD - ' . $_SERVER['REQUEST_METHOD']);

		return $params;
	}

	function checkTransaction($pmconfigs, $order, $act)
	{
		$request = Factory::getApplication()->getInput()->getString('Data');
		$signature = Factory::getApplication()->getInput()->getString('Signature');

		if (!isset($request)) {
			$status = 2;
			return array($status, $order->order_number, '', array());
		}

		$data = htmlspecialchars_decode($request);

		try {
			$data = json_decode($data);
		} catch (Exception $e) {
			$this->log_error('checkTransaction', "Fail to parse the server response; RESPONSE - " . htmlspecialchars_decode($request));

			$this->notify_fail($request);
		}

		$status = 0;
		$secret_word = $pmconfigs['expresspay_secret_key_notify'];
		$is_use_signature = (isset($pmconfigs['expresspay_sign_notify']) && $pmconfigs['expresspay_sign_notify'] == 'on') ? true : false;

		if ($is_use_signature && $signature != $this->compute_signature($request, $secret_word)) {
			$this->notify_fail($request);

			return array(0, $order->order_number, '', array());
		}

		if (isset($data->CmdType)) {
			switch ($data->CmdType) {
				case '1':
					$status = 1;
					$this->log_info('checkTransaction', 'Initialization to update status. STATUS ID - ' . $status . 'ORDER NUM ' . $order->order_number);
					break;
				case '2':
					$status = 3;
					$this->log_info('checkTransaction', 'Initialization to update status. STATUS ID - ' . $status . 'ORDER NUM ' . $order->order_number);
					break;
				case '3':
					if ($data->Status == 3 || $data->Status == 6) {
						$status = 1;
						$this->log_info('checkTransaction', 'Initialization to update status. STATUS ID - ' . $status . 'ORDER NUM ' . $order->order_number);
					}
					break;
				default:
					$this->notify_fail($request['Data']);
					return array(0, $order->order_number);
			}

			header("HTTP/1.0 200 OK");
			echo 'SUCCESS';

			return array($status, $order->order_number, '', array());
		} else {
			$this->notify_fail($request['Data']);

			return array(0, $order->order_number, '', array());
		}

		return array(0, $order->order_number, '', array());
	}

	function notify_fail($dataJSON)
	{
		$this->log_error('notify_fail', "Fail to update status; RESPONSE - " . htmlspecialchars_decode($dataJSON));

		header("HTTP/1.0 400 Bad Request");
		echo 'FAILED | Incorrect digital signature';
	}

	function compute_signature($json, $secret_word)
	{
		$hash = NULL;
		$secret_word = trim($secret_word);

		if (empty($secret_word))
			$hash = strtoupper(hash_hmac('sha1', $json, ""));
		else
			$hash = strtoupper(hash_hmac('sha1', $json, $secret_word));

		return $hash;
	}

	function compute_signature_add_invoice($request_params, $secret_word, $token)
	{
		$secret_word = trim($secret_word);
		$normalized_params = array_change_key_case($request_params, CASE_LOWER);
		$api_method = array(
			"token",
			"serviceid",
			"accountno",
			"amount",
			"currency",
			"expiration",
			"info",
			"surname",
			"firstname",
			"patronymic",
			"city",
			"street",
			"house",
			"building",
			"apartment",
			"isnameeditable",
			"isaddresseditable",
			"isamounteditable",
			"emailnotification",
			"smsphone",
			"returntype",
			"returnurl",
			"failurl",
		);

		$result = $token;

		foreach ($api_method as $item)
			$result .= (isset($normalized_params[$item])) ? $normalized_params[$item] : '';

		$hash = strtoupper(hash_hmac('sha1', $result, $secret_word));

		return $hash;
	}

	function log_error_exception($name, $message, $e)
	{
		$this->log($name, "ERROR", $message . '; EXCEPTION MESSAGE - ' . $e->getMessage() . '; EXCEPTION TRACE - ' . $e->getTraceAsString());
	}

	function log_error($name, $message)
	{
		$this->log($name, "ERROR", $message);
	}

	function log_info($name, $message)
	{
		$this->log($name, "INFO", $message);
	}

	function log($name, $type, $message)
	{
		\JSHelper::saveToLog("expresspay/express-pay-" . date('Y.m.d') . ".log", $type . " - IP - " . $_SERVER['REMOTE_ADDR'] . "; USER AGENT - " . $_SERVER['HTTP_USER_AGENT'] . "; FUNCTION - " . $name . "; MESSAGE - " . $message . ';');
	}
}
?>