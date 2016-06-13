<?php
/**
 * @package    zarinpalzg payment module
 * @author     Masoud Amini
 * @copyright  2014  MasoudAmini.ir
 * @version    1.00
 */
@session_start();
if (isset($_GET['do'])) {
	include (dirname(__FILE__) . '/../../config/config.inc.php');
	include (dirname(__FILE__) . '/../../header.php');
	include_once (dirname(__FILE__) . '/zarinpalzg.php');
	$zarinpalzg = new zarinpalzg;
//	if (!$cookie -> isLogged())
//		Tools::redirect('authentication.php?back=order.php');
	if ($_GET['do'] == 'payment') {
		//if (isset($_POST['id'])) {
			$zarinpalzg -> do_payment($cart);
	//	} else {
		//	echo $zarinpalzg -> error($zarinpalzg -> l('There is a problem.'));
		//}
	} else {
		if (isset($_GET['id']) && isset($_GET['amount']) && isset($_GET['Authority']) && isset($_GET['Status'])) {
			$orderId = $_GET['id'];
			$amount = $_GET['amount'];
			if (isset($_SESSION['order' . $orderId])) {
				$hash = Configuration::get('zarinpalzg_HASH');
				$hash = md5($orderId . $amount . $hash);
				if ($hash == $_SESSION['order' . $orderId]) {
					$api = Configuration::get('zarinpalzg_API');
					$client = new SoapClient('https://de.zarinpal.com/pg/services/WebGate/wsdl', array('encoding' => 'UTF-8')); 
					
					$params = array(
								'MerchantID' => $api ,  // this is our PIN NUMBER
								'Authority' => $_GET["Authority"],
								'Amount' => $amount
							) ; // to see if we can change it
					$result = $client->PaymentVerification($params);
				
					if (!empty($result->Status) and $result->Status == 100) {
						error_reporting(E_ALL);
						$au = $_GET['Authority'];
						$zarinpalzg -> validateOrder($orderId, _PS_OS_PAYMENT_, $amount, $zarinpalzg -> displayName, "سفارش تایید شده / کد رهگیری {$au}", array(), $cookie -> id_currency);
						$_SESSION['order' . $orderId] = '';
						Tools::redirect('history.php');
					} else {
						echo $zarinpalzg -> error($zarinpalzg -> l('There is a problem.') . ' (' . $result->Status . ')<br/>' . $zarinpalzg -> l('Authority code') . ' : ' . $_GET['Authority']);
					}

				} else {
					echo $zarinpalzg -> error($zarinpalzg -> l('There is a problem.'));
				}
			} else {
				echo $zarinpalzg -> error($zarinpalzg -> l('There is a problem.'));
			}
		} else {
			echo $zarinpalzg -> error($zarinpalzg -> l('There is a problem.'));
		}
	}
	include_once (dirname(__FILE__) . '/../../footer.php');
} else {
	_403();
}
function _403() {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
