<?php
defined('_JEXEC') or die('Restricted access');
define("ERIP_EXPRESSPAY_VERSION", "2.4");

class pm_erip_expresspay extends PaymentRoot {

	function loadLanguageFile() {
        $lang = JFactory::getLanguage();
        $langtag = $lang->getTag();

        if (file_exists(JPATH_ROOT . '/components/com_jshopping/payments/pm_erip_expresspay/lang/' . $langtag . '.php'))
            require_once(JPATH_ROOT . '/components/com_jshopping/payments/pm_erip_expresspay/lang/' . $langtag . '.php');
        else
            require_once(JPATH_ROOT . '/components/com_jshopping/payments/pm_erip_expresspay/lang/ru-RU.php');

        return substr($langtag, 0, 2);
    }

	function showAdminFormParams($params) {
		$array_params = array('erip_expresspay_token', 'erip_expresspay_sign_invoices', 'erip_expresspay_secret_key', 'erip_expresspay_sign_notify', 'erip_expresspay_secret_key_notify', 'erip_expresspay_name_editable', 'erip_expresspay_address_editable', 'erip_expresspay_amount_editable', 'erip_expresspay_test_mode', 'erip_expresspay_url_api', 'erip_expresspay_url_sandbox_api', 'transaction_end_status', 'transaction_pending_status', 'transaction_failed_status', 'erip_expresspay_test_mode');

		$lang = $this->loadLanguageFile();

		foreach ($array_params as $key) {
			if (!isset($params[$key]) || (isset($params[$key]) && empty($params[$key]))) 
				switch ($key) {
					case 'erip_expresspay_url_sandbox_api':
						$params[$key] = 'https://sandbox-api.express-pay.by';
						
						break;
					case 'erip_expresspay_url_api':
						$params[$key] = 'https://api.express-pay.by';

						break;
					case 'erip_expresspay_message_success':
						$params[$key] = _JSHOP_MESSAGE_SUCCESS;

						break;
					default:
						$params[$key] = '';

						break;
				}
		}

		$orders = JModelLegacy::getInstance('orders', 'JshoppingModel');
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);		
		$query->select($db->quoteName(array($lang)))->from($db->quoteName('#__jshopping_payment_express_pay'));
		$db->setQuery($query);
		$message_success = $db->loadResult();

		include(dirname(__FILE__)."/adminparamsform.php");
	}

	function showEndForm($pmconfigs, $order) {
		$this->log_info('showEndForm', 'Initialization request for add invoice');

		$jshop_config   = JSFactory::getConfig();
		$item_name     = sprintf(_JSHOP_PAYMENT_NUMBER, $order->order_number);

		$secret_word = $pmconfigs['erip_expresspay_secret_key'];
		$is_use_signature = ( $pmconfigs['erip_expresspay_sign_invoices'] == 'on' ) ? true : false;

		$url = ( $pmconfigs['erip_expresspay_test_mode'] != 'on' ) ? $pmconfigs['erip_expresspay_url_api'] : $pmconfigs['erip_expresspay_url_sandbox_api'];
		$url .= "/v1/invoices?token=" . $pmconfigs['erip_expresspay_token'];

		$currency = (date('y') > 16 || (date('y') >= 16 && date('n') >= 7)) ? '933' : '974';

        $request_params = array(
            "AccountNo" => $order->order_number, 
            "Amount" => getPriceFromCurrency($order->order_total),
            "Currency" => $currency,
            "Surname" => $order->l_name,
            "FirstName" => $order->f_name,
            "City" => $order->city,
            "IsNameEditable" => ( ( $pmconfigs['erip_expresspay_name_editable'] == 'on' ) ? 1 : 0 ),
            "IsAddressEditable" => ( ( $pmconfigs['erip_expresspay_address_editable'] == 'on' ) ? 1 : 0 ),
            "IsAmountEditable" => ( ( $pmconfigs['erip_expresspay_amount_editable'] == 'on' ) ? 1 : 0 )
        );

        $token = $pmconfigs['erip_expresspay_token'];

        if($is_use_signature)
        	$url .= "&signature=" . $this->compute_signature_add_invoice($request_params, $secret_word, $token);

        $request_params = http_build_query($request_params);

        $this->log_info('showEndForm', 'Send POST request; ORDER ID - ' . $order->order_number . '; URL - ' . $url . '; REQUEST - ' . $request_params);

        $response = "";
		$app = JFactory::getApplication();

        try {
	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $url);
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_params);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	        $response = curl_exec($ch);
	        curl_close($ch);
    	} catch (Exception $e) {
    		$this->log_error_exception('showEndForm', 'Get response; ORDER ID - ' . $order->order_number . '; RESPONSE - ' . $response, $e);

			$app->redirect('/index.php?option=com_jshopping&controller=checkout&task=step7&act=cancel&js_paymentclass=pm_erip_expresspay');    		
    	}

    	$this->log_info('showEndForm', 'Get response; ORDER ID - ' . $order->order_number . '; RESPONSE - ' . $response);

		try {
        	$response = json_decode($response);
    	} catch (Exception $e) {
    		$this->log_error_exception('showEndForm', 'Get response; ORDER ID - ' . $order->order_number . '; RESPONSE - ' . $response, $e);

    		$app->redirect('/index.php?option=com_jshopping&controller=checkout&task=step7&act=cancel&js_paymentclass=pm_erip_expresspay');  
    	}

    	$this->log_info('showEndForm', 'End request for add invoice');

        if(isset($response->InvoiceNo))
        	$this->success();
        else {
			$this->log_info('showEndForm', 'Render fail page; ORDER ID - ' . $order->order_number);

        	$this->fail();
        }
	}

	function success() {
		$app = JFactory::getApplication();
		$app->redirect('/index.php?option=com_jshopping&controller=checkout&task=step7&act=return&js_paymentclass=pm_erip_expresspay');
	}

	function fail() {
		$app = JFactory::getApplication();
		$app->redirect('/index.php?option=com_jshopping&controller=checkout&task=step7&act=cancel&js_paymentclass=pm_erip_expresspay');
	}

	function getUrlParams($pmconfigs) {
		$this->log_info('getUrlParams', 'Get notify from server; REQUEST METHOD - ' . $_SERVER['REQUEST_METHOD']);
        $params = array(); 

        if($_SERVER['REQUEST_METHOD'] === 'POST' && JRequest::getString("act") == 'save') {
		    $lang = ( !empty($_POST['lang']) ) ? $_POST['lang'] : 'ru';

		    $user = JFactory::getUser(JRequest::getInt("id"));

		    if(empty(JRequest::getString("data")) || md5($user->password) != JRequest::getString("token"))
		    	jexit("false");

			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
 
			$fields = array(
			    $db->quoteName($lang) . ' = ' . $db->quote(JRequest::getString("data"))
			);
 
			$query->update($db->quoteName('#__jshopping_payment_express_pay'))->set($fields);
			$db->setQuery($query);
			$db->execute();	

			jexit("true");
        }

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$data = htmlspecialchars_decode(JRequest::getString("Data"));

			try {
				if(!empty($data))
					$data = json_decode($data);

        		$params['order_id'] = $data->AccountNo;
			} catch (Exception $e) {
				$this->log_error('getUrlParams', "Fail to parse the server response; RESPONSE - " . htmlspecialchars_decode(JRequest::getString("Data")));

				$params['order_id'] = 0;
			}

		}

        $params['hash'] = "";
        $params['checkHash'] = false;
        $params['checkReturnParams'] = false;

        $this->log_info('getUrlParams', 'End (Get notify from server); REQUEST METHOD - ' . $_SERVER['REQUEST_METHOD']);

		return $params;
	}

	function checkTransaction($params, $order, $act) {
		$request = JRequest::get();

		$data = htmlspecialchars_decode($request['Data']);

		try {
        	$data = json_decode($data);
    	} catch(Exception $e) {
    		$this->log_error('checkTransaction', "Fail to parse the server response; RESPONSE - " . htmlspecialchars_decode($request['Data']));

    		$this->notify_fail($request['Data']);
    	}

    	$status = 0;
		$secret_word = $pmconfigs['erip_expresspay_secret_key_notify'];
		$is_use_signature = ( $pmconfigs['erip_expresspay_sign_notify'] == 'on' ) ? true : false;
		$signature = $request['Signature'];

    	if($is_use_signature && $signature != $this->compute_signature($data, $secret_word)) {
	    	$this->notify_fail($request['Data']);

	    	return array(0, $order->order_number);
    	}

        if(isset($data->CmdType)) {
        	switch ($data->CmdType) {
        		case '1':
        			$status = $params['transaction_end_status'] - 1;
        			$this->log_info('checkTransaction', 'Initialization to update status. STATUS ID - ' . $status . "; RESPONSE - " . htmlspecialchars_decode($request['Data']));

        			break;
        		case '2':
        			$status = $params['transaction_failed_status'];
        			$this->log_info('checkTransaction', 'Initialization to update status. STATUS ID - ' . $status . "; RESPONSE - " . htmlspecialchars_decode($request['Data']));

        			break;
        		default:
					$this->notify_fail($request['Data']);

					return array(0, $order->order_number);
        	}

	    	header("HTTP/1.0 200 OK");
	    	echo 'SUCCESS';

	    	return array($status, $order->order_number);
        } else {
			$this->notify_fail($request['Data']);	

			return array(0, $order->order_number);
        }

        return array(0, $order->order_number);
	}

	function notify_fail($dataJSON) {
		$this->log_error('notify_fail', "Fail to update status; RESPONSE - " . htmlspecialchars_decode($dataJSON));

		header("HTTP/1.0 400 Bad Request");
		echo 'FAILED | Incorrect digital signature';
	}
	
	function complete($pmconfigs, $order, $payment) {
		$this->log_info('complete', 'Initialization render success page; ORDER ID - ' . $order->order_number);

		$lang = $this->loadLanguageFile();
		$test_mode = $pmconfigs['erip_expresspay_test_mode'];

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);		
		$query->select($db->quoteName(array($lang)))->from($db->quoteName('#__jshopping_payment_express_pay'));
		$db->setQuery($query);
		$message_success = $db->loadResult();

		$message_success = nl2br($message_success, true);
		$message_success = str_replace("##order_id##", $order->order_number, $message_success);

		$signature_success = $signature_cancel = "";

		if($pmconfigs['erip_expresspay_sign_notify']) {
			$secret_word = $pmconfigs['erip_expresspay_secret_key_notify'];

			$signature_success = $this->compute_signature('{"CmdType": 1, "AccountNo": ' . $order->order_number . '}', $secret_word);
			$signature_cancel = $this->compute_signature('{"CmdType": 2, "AccountNo": ' . $order->order_number . '}', $secret_word);
		}
	?>
		<h2><?php echo _JSHOP_HEADING_TITLE; ?></h2>
		<p><?php echo _JSHOP_TEXT_MESSAGE . $order->order_number; ?></p>
		<p><?php echo $message_success; ?></p>
		<div class="buttons">
			<?php if($test_mode) : ?>
				<?php echo _JSHOP_TEST_MODE_LABEL_FRONT; ?><br/>
				<input style="margin-right: 4px;" type="button" id="send_notify_success" class="btn btn-primary" value="<?php echo _JSHOP_SEND_NOTIFY_SUCCESS; ?>" />
				<input type="button" id="send_notify_cancel" class="btn btn-primary" value="<?php echo _JSHOP_SEND_NOTIFY_CANCEL; ?>" />

				<script type="text/javascript">
					jQuery(document).ready(function() {
						jQuery('#send_notify_success').click(function() {
							send_notify(1, '<?php echo $signature_success; ?>');
						});

						jQuery('#send_notify_cancel').click(function() {
							send_notify(2, '<?php echo $signature_cancel; ?>');
						});

						function send_notify(type, signature) {
							jQuery.post('<?php echo JURI::root() . "index.php?option=com_jshopping&controller=checkout&task=step7&act=notify&js_paymentclass=pm_erip_expresspay" ?>', 'Data={"CmdType": ' + type + ', "AccountNo": <?php echo '"' . $order->order_number . '"'; ?>}&Signature=' + signature, function(data) {alert(data);})
							.fail(function(data) {
						  		alert(data.responseText);
							});
						}
					});
				</script>
			<?php endif; ?>
		</div>

	<?php

		$this->log_info('complete', 'End render success page; ORDER ID - ' . $order->order_number);
 	}

	function compute_signature($json, $secret_word) {
	    $hash = NULL;
	    $secret_word = trim($secret_word);

	    if(empty($secret_word))
			$hash = strtoupper(hash_hmac('sha1', $json, ""));
	    else
	        $hash = strtoupper(hash_hmac('sha1', $json, $secret_word));

	    return $hash;
	}	

    function compute_signature_add_invoice($request_params, $secret_word, $token) {
    	$secret_word = trim($secret_word);
        $normalized_params = array_change_key_case($request_params, CASE_LOWER);
        $api_method = array(
                "accountno",
                "amount",
                "currency",
                // "expiration",
                // "info",
                "surname",
                "firstname",
                // "patronymic",
                "city",
                // "street",
                // "house",
                // "building",
                // "apartment",
                "isnameeditable",
                "isaddresseditable",
                "isamounteditable"
        );

        $result = $token;

        foreach ($api_method as $item)
            $result .= ( isset($normalized_params[$item]) ) ? $normalized_params[$item] : '';

        $hash = strtoupper(hash_hmac('sha1', $result, $secret_word));

        return $hash;
    }

    function log_error_exception($name, $message, $e) {
    	$this->log($name, "ERROR" , $message . '; EXCEPTION MESSAGE - ' . $e->getMessage() . '; EXCEPTION TRACE - ' . $e->getTraceAsString());
    }

    function log_error($name, $message) {
    	$this->log($name, "ERROR" , $message);
    }

    function log_info($name, $message) {
    	$this->log($name, "INFO" , $message);
    }

    function log($name, $type, $message) {
		saveToLog("erip_expresspay/express-pay-" . date('Y.m.d') . ".log", $type . " - IP - " . $_SERVER['REMOTE_ADDR'] . "; USER AGENT - " . $_SERVER['HTTP_USER_AGENT'] . "; FUNCTION - " . $name . "; MESSAGE - " . $message . ';');
    }
}
?>