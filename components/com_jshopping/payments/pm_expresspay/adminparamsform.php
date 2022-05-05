<div class="col100">
	<fieldset class="adminform">
		<table class="admintable">
			<tr>
			  <td class="key"><a target="_blank" href="https://express-pay.by"><img src="/components/com_jshopping/payments/pm_expresspay/image/expresspay_big.png" width="270" height="91" alt="exspress-pay.by" title="express-pay.by"></a></td>
			  <td style="padding-top: 0;"><?php

use Joomla\CMS\HTML\HTMLHelper;

 echo _JSHOP_TEXT_ABOUT; ?></td>
			</tr>
		</table>
		<table class="admintable">
			<tr>
				<td class="key"><?php echo _JSHOP_SERVICE_ID_LABEL; ?></td>
				<td>
					<input type="text" style="width: 800px;" class="inputbox" name="pm_params[expresspay_service_id]" value="<?php echo $params['expresspay_service_id']?>" />
					<?php echo \JSHelperAdmin::tooltip(_JSHOP_SERVICE_ID_COMMENT) ?>
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo _JSHOP_TOKEN_LABEL; ?></td>
				<td>
					<input type="text" style="width: 800px;" class="inputbox" name="pm_params[expresspay_token]" value="<?php echo $params['expresspay_token']?>" />
					<?php echo \JSHelperAdmin::tooltip(_JSHOP_TOKEN_COMMENT) ?>
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo _JSHOP_HANDLER_LABEL; ?></td>
				<td>
					<input type="text" style="width: 800px;" disabled="disabled" class="inputbox" name="expresspay_handler_url" value="<?php echo str_replace('/administrator', '', JURI::root() . 'index.php?option=com_jshopping&controller=checkout&task=step7&act=notify&js_paymentclass=pm_expresspay'); ?>" />
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo _JSHOP_SECRET_KEY_LABEL; ?></td>
				<td>
					<input type="text" style="width: 800px;" class="inputbox" name="pm_params[expresspay_secret_key]" value="<?php echo $params['expresspay_secret_key']; ?>" />
					<?php echo \JSHelperAdmin::tooltip(_JSHOP_SECRET_KEY_COMMENT)?>
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo _JSHOP_SIGN_NOTIFY_LABEL; ?></td>
				<td>
					<input type="checkbox" class="inputbox" name="pm_params[expresspay_sign_notify]" <?php echo ( $params['expresspay_sign_notify'] == 'on' ) ? 'checked' : ''; ?> />
					<?php echo \JSHelperAdmin::tooltip(_JSHOP_SIGN_COMMENT)?>
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo _JSHOP_SECRET_KEY_NOTIFY_LABEL; ?></td>
				<td>
					<input type="text" style="width: 800px;" class="inputbox" name="pm_params[expresspay_secret_key_notify]" value="<?php echo $params['expresspay_secret_key_notify']; ?>" />
					<?php echo \JSHelperAdmin::tooltip(_JSHOP_SECRET_KEY_COMMENT)?>
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo _JSHOP_NAME_EDITABLE_LABEL; ?></td>
				<td>
					<input type="checkbox" class="inputbox" name="pm_params[expresspay_name_editable]" <?php echo ( $params['expresspay_name_editable'] == 'on' ) ? 'checked' : ''; ?> />
					<?php echo \JSHelperAdmin::tooltip(_JSHOP_NAME_EDITABLE_COMMENT)?>
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo _JSHOP_ADDRESS_EDITABLE_LABEL; ?></td>
				<td>
					<input type="checkbox" class="inputbox" name="pm_params[expresspay_address_editable]" <?php echo ( $params['expresspay_address_editable'] == 'on' ) ? 'checked' : ''; ?> />
					<?php echo \JSHelperAdmin::tooltip(_JSHOP_ADDRESS_EDITABLE_COMMENT)?>
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo _JSHOP_AMOUNT_EDITABLE_LABEL; ?></td>
				<td>
					<input type="checkbox" class="inputbox" name="pm_params[expresspay_amount_editable]" <?php echo ( $params['expresspay_amount_editable'] == 'on' ) ? 'checked' : ''; ?> />
					<?php echo \JSHelperAdmin::tooltip(_JSHOP_AMOUNT_EDITABLE_COMMENT)?>
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo _JSHOP_TEST_MODE_LABEL; ?></td>
				<td>
					<input type="checkbox" class="inputbox" name="pm_params[expresspay_test_mode]" <?php echo ( $params['expresspay_test_mode'] == 'on' ) ? 'checked' : ''; ?> />
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo _JSHOP_URL_API_LABEL; ?></td>
				<td>
					<input type="text" style="width: 800px;" class="inputbox" name="pm_params[expresspay_url_api]" value="<?php echo $params['expresspay_url_api']; ?>" />
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo _JSHOP_URL_SANDBOX_API_LABEL; ?></td>
				<td>
					<input type="text" style="width: 800px;" class="inputbox" name="pm_params[expresspay_url_sandbox_api]" value="<?php echo $params['expresspay_url_sandbox_api']; ?>" />
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo _JSHOP_MESSAGE_SUCCESS_LABEL; ?></td>
				<td>
					<textarea style="height: 120px; width: 800px; max-width: 800px;" id="expresspay_message_success" name="expresspay_message_success"><?php echo $message_success; ?></textarea>
				</td>
			</tr>
			<tr>
				<td class="key"><span style="font-size: 19px;font-weight: bold;"><?php echo _JSHOP_SETTINGS_MODULE_LABEL; ?></span></td>
				<td></td>
			</tr>
			<tr>
				<td class="key"><?php echo _JSHOP_STATUS_END?></td>
				<td><?php print HTMLHelper::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[transaction_end_status]', 'class = "inputbox" ',	'status_id', 'name', $params['transaction_end_status'] );
					print " " . \JSHelperAdmin::tooltip(_JSHOP_STATUS_END);
				?></td>
			</tr>
			<tr>
				<td class="key"><?php echo _JSHOP_STATUS_PENDING?></td>
				<td><?php print HTMLHelper::_('select.genericlist',$orders->getAllOrderStatus(), 'pm_params[transaction_pending_status]', 'class = "inputbox" ', 'status_id', 'name', $params['transaction_pending_status']);
					print " " . \JSHelperAdmin::tooltip(_JSHOP_STATUS_PENDING);?></td>
			</tr>
			<tr>
				<td class="key"><?php echo _JSHOP_STATUS_FAILED;?></td>
				<td><?php print HTMLHelper::_('select.genericlist',$orders->getAllOrderStatus(), 'pm_params[transaction_failed_status]', 'class = "inputbox" ', 'status_id', 'name', $params['transaction_failed_status']);
					print " " . \JSHelperAdmin::tooltip(_JSHOP_STATUS_FAILED);
				?></td>
			</tr>
		</table>
	</fieldset>
</div>
<div class="copyright" style="text-align: center;margin-top: 20px;">
  &copy; Все права защищены | ООО «ТриИнком», 2013-<?php echo date('Y'); ?><br/>
  <?php echo _JSHOP_TEXT_VERSION . EXPRESSPAY_VERSION ?>
</div>
<div class="clr"></div>

<script type="text/javascript">
	<?php $user = JFactory::getUser(); ?>

	jQuery(document).ready(function() {
		jQuery('#adminForm').submit(function() {
			var lang = '<?php echo $lang; ?>';
			var token = '<?php echo md5($user->password); ?>';
			var id = '<?php echo $user->id; ?>';
		      
			jQuery.post('<?php echo JURI::root() . "/index.php?option=com_jshopping&controller=checkout&task=step7&act=save&js_paymentclass=pm_expresspay" ?>', 'data=' + jQuery("#expresspay_message_success").val() + "&token=" + token + '&lang=' + lang + '&id=' + id);
		});
	});
</script>
