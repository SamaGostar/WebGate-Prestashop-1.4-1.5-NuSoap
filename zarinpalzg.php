<?php
/**
 * @package    zarinpalzg payment module
 * @author     Masoud Amini
 * @copyright  2014  MasoudAmini.ir
 * @version    1.00
 */
if (!defined('_PS_VERSION_'))
	exit ;
class zarinpalzg extends PaymentModule {

	private $_html = '';
	private $_postErrors = array();

	public function __construct() {

		$this->name = 'zarinpalzg';
		$this->tab = 'payments_gateways';
		$this->version = '1.1';
		$this->author = 'Masoud Amini';
		$this->currencies = true;
		$this->currencies_mode = 'radio';
		parent::__construct();
		$this->displayName = $this->l('zarinpal(zaringate) Payment Modlue');
		$this->description = $this->l('Online Payment With zarinpal(zaringate)');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details?');
		if (!sizeof(Currency::checkPaymentCurrencies($this->id)))
			$this->warning = $this->l('No currency has been set for this module');
		$config = Configuration::getMultiple(array('zarinpalzg_API'));
		if (!isset($config['zarinpalzg_API']))
			$this->warning = $this->l('You have to enter your zarinpal merchant key to use zarinpalzg for your online payments.');

	}

	public function install() {
		if (!parent::install() || !Configuration::updateValue('zarinpalzg_API', '') || !Configuration::updateValue('zarinpalzg_LOGO', '') || !Configuration::updateValue('zarinpalzg_HASH_KEY', $this->hash_key()) || !$this->registerHook('payment') || !$this->registerHook('paymentReturn'))
			return false;
		else
			return true;
	}

	public function uninstall() {
		if (!Configuration::deleteByName('zarinpalzg_API') || !Configuration::deleteByName('zarinpalzg_LOGO') || !Configuration::deleteByName('zarinpalzg_HASH_KEY') || !parent::uninstall())
			return false;
		else
			return true;
	}

	public function hash_key() {
		$en = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
		$one = rand(1, 26);
		$two = rand(1, 26);
		$three = rand(1, 26);
		return $hash = $en[$one] . rand(0, 9) . rand(0, 9) . $en[$two] . $en[$tree] . rand(0, 9) . rand(10, 99);
	}

	public function getContent() {

		if (Tools::isSubmit('zarinpalzg_setting')) {

			Configuration::updateValue('zarinpalzg_API', $_POST['zp_API']);
			Configuration::updateValue('zarinpalzg_LOGO', $_POST['zp_LOGO']);
			$this->_html .= '<div class="conf confirm">' . $this->l('Settings updated') . '</div>';
		}

		$this->_generateForm();
		return $this->_html;
	}

	private function _generateForm() {
		$this->_html .= '<div align="center"><form action="' . $_SERVER['REQUEST_URI'] . '" method="post">';
		$this->_html .= $this->l('Enter your pin :') . '<br/><br/>';
		$this->_html .= '<input type="text" name="zp_API" value="' . Configuration::get('zarinpalzg_API') . '" ><br/><br/>';
		$this->_html .= '<input type="submit" name="zarinpalzg_setting"';
		$this->_html .= 'value="' . $this->l('Save it!') . '" class="button" />';
		$this->_html .= '</form><br/></div>';
	}

	public function do_payment($cart) {
		error_reporting(E_ALL);
		ini_set('display_errors', 1);


		$client = new SoapClient('https://de.zarinpal.com/pg/services/WebGate/wsdl', array('encoding' => 'UTF-8')); 
		
		$ZarinpalPin = Configuration::get('zarinpalzg_API');
		$amount = floatval(number_format($cart ->getOrderTotal(true, 3), 2, '.', ''));
		$callbackUrl = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__ . 'modules/zarinpalzg/zp.php?do=call_back&id=' . $cart ->id . '&amount=' . $amount;
		$orderId = $cart ->id;
		$txt = 'پرداخت سفارش شماره: ' . $cart ->id;
		$params = array(
						'MerchantID' =>  $ZarinpalPin,
		                'Amount' => $amount,
						'Description' => $txt,
						'Email'                 => $Email,
                        'Mobile'                 => $Mobile,
						'CallbackURL' => $callbackUrl
						
		              );

		$res = $client->PaymentRequest($params);
		$hash = Configuration::get('zarinpalzg_HASH');
		$_SESSION['order' . $orderId] = md5($orderId . $amount . $hash);
		if ($res->Status == '') {
			echo $this->error($this->l('There is a problem.'));
		} elseif ($res->Status < 1) {
			echo $this->error($this->l('There is a problem.') . ' (' . $res->Status . ')');
		} else {
			echo $this->success($this->l('Redirecting...'));
			echo '<script>window.location=("https://www.zarinpal.com/pg/StartPay/' . $res->Authority . '/ZarinGate");</script>';
		}
	}

	public function error($str) {
		return '<div class="alert error">' . $str . '</div>';
	}

	public function success($str) {
		echo '<div class="conf confirm">' . $str . '</div>';
	}

	public function hookPayment($params) {
		global $smarty;
		$smarty ->assign('zarinpalzg_logo', Configuration::get('zarinpalzg_LOGO'));
		if ($this->active)
			return $this->display(__FILE__, 'zppayment.tpl');
	}

	public function hookPaymentReturn($params) {
		if ($this->active)
			return $this->display(__FILE__, 'zpconfirmation.tpl');
	}

}

// End of: zarinpalzg.php
?>
