<?php  
/*PrestaShop zarinpal Payment (New Webservice)
* PrestaTools.Ir */
class zarinpalpayment extends PaymentModule{  

	private $_html = '';
	private $_postErrors = array();

	public function __construct(){  

		$this->name = 'zarinpalpayment';  
		$this->tab = 'payments_gateways';  
		$this->version = '1.1';  
		$this->author = 'Www.PrestaTools.Ir';
		
		$this->currencies = true;
  		$this->currencies_mode = 'radio';
		
		parent::__construct();  		
		
		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('Zarinpal Payment');  
		$this->description = $this->l('A free module to pay online for Zarinpal.');  
		$this->confirmUninstall = $this->l('Are you sure, you want to delete your details?');

		if (!sizeof(Currency::checkPaymentCurrencies($this->id)))
			$this->warning = $this->l('No currency has been set for this module');

		$config = Configuration::getMultiple(array('ZARINPAL_PIN', ''));			
		if (!isset($config['ZARINPAL_PIN']))
			$this->warning = $this->l('Your Zarinpal Pin Code must be configured in order to use this module');

		$config = Configuration::getMultiple(array('ZARINPAL_HASHKEY', ''));			
		if (!isset($config['ZARINPAL_HASHKEY']))
			$this->warning = $this->l('Your web site Hash Key must be configured in order to use this module');

			
		if ($_SERVER['SERVER_NAME'] == 'localhost')
			$this->warning = $this->l('Your are in localhost, Zarinpal Payment can\'t validate order');


	}  
	public function install(){
		if (!parent::install()
	    	OR !Configuration::updateValue('ZARINPAL_PIN', '')
	    	OR !Configuration::updateValue('ZARINPAL_HASHKEY', '')
	      	OR !$this->registerHook('payment')
	      	OR !$this->registerHook('paymentReturn')){
			    return false;
		}else{
		    return true;
		}
	}
	public function uninstall(){
		if (!Configuration::deleteByName('ZARINPAL_PIN') 
			OR !Configuration::deleteByName('ZARINPAL_HASHKEY') 
			OR !parent::uninstall())
			return false;
		return true;
	}
	
	public function displayFormSettings()
	{
		$this->_html .= '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<fieldset>
				<legend><img src="../img/admin/cog.gif" alt="" class="middle" />'.$this->l('Settings').'</legend>
				<label>'.$this->l('Zarinpal PIN').'</label>
				<div class="margin-form"><input type="text" size="30" name="ZarinpalPin" value="'.Configuration::get('ZARINPAL_PIN').'" /></div>
				<label>'.$this->l('Your web site Hash Key').'</label>
				<div class="margin-form"><input type="text" size="30" name="HashKey" value="'.Configuration::get('ZARINPAL_HASHKEY').'" />
				<p class="hint clear" style="display: block; width: 501px;">'.$this->l('This hash key should be a secret code for your site.(Please combine an string contain your site name and a date string)').'</p></div>
				<center><input type="submit" name="submitZarinpal" value="'.$this->l('Update Settings').'" class="button" /></center>			
			</fieldset>
		</form>';
	}

	public function displayConf()
	{
		$this->_html .= '
		<div class="conf confirm">
			<img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />
			'.$this->l('Settings updated').'
		</div>';
	}
	
	public function displayErrors()
	{
		foreach ($this->_postErrors AS $err)
		$this->_html .= '<div class="alert error">'. $err .'</div>';
	}

       	public function getContent()
	{
		$this->_html = '<h2>'.$this->l('Zarinpal Payment').'</h2>';
		if (isset($_POST['submitZarinpal']))
		{
			if (empty($_POST['ZarinpalPin']))
				$this->_postErrors[] = $this->l('Zarinpal PIN is required.');
			if (empty($_POST['HashKey']))
				$this->_postErrors[] = $this->l('Your Hash Key is required.');
			if (!sizeof($this->_postErrors))
			{
				Configuration::updateValue('ZARINPAL_PIN', $_POST['ZarinpalPin']);
				Configuration::updateValue('ZARINPAL_HASHKEY', $_POST['HashKey']);
				$this->displayConf();
			}
			else
				$this->displayErrors();
		}

		$this->displayFormSettings();
		return $this->_html;
	}

	private function displayZarinpalPayment()
	{
		$this->_html .= '<img src="../modules/zarinpalpayment/zarinpal.png" style="float:left; margin-right:15px;"><b>'.$this->l('This module allows you to accept payments by Zarinpal.').'</b><br /><br />
		'.$this->l('Any cart from Shetab Banks are accepted.').'<br /><br /><br />';

	}

	public function execPayment($cart){
        include('nusoap.php');
		global $cookie, $smarty;


  		$client = new nusoap_client('https://de.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');
		$client->soap_defencoding = 'UTF-8';
		if (!$err = $client->getError())
		   $soapProxy = $client->getProxy() ;
		if ( (!$client) OR ($err = $client->getError()) ) {
				$this->_postErrors[] = $this->l('Could not connect to bank or service.');
			   	$this->displayErrors();

  		} else {
			$authority = 0 ;  // default authority
			$status = 1 ;	// default status
			$ParsURL = 'payment.php';		
			$OrderDesc = Configuration::get('PS_SHOP_NAME'). $this->l(' Order'); 					
			$purchase_currency = new Currency($rial_id);
			$current_currency = new Currency($cookie->id_currency);

				if($cookie->id_currency == $purchase_currency->id)
					$PurchaseAmount = number_format($cart->getOrderTotal(true, 3), 0, '', '');
				else
					$PurchaseAmount= number_format($this->convertPriceFull($cart->getOrderTotal(true, 3), $current_currency, $purchase_currency), 0, '', '');					
					

			$ZarinpalPin = Configuration::get('ZARINPAL_PIN');			
			$OrderId = $cart->id.substr( time(), -2);

		
			$hash_data = 'o='.$cart->id.$PurchaseAmount;
			$hash = crypt($hash_data, Configuration::get('ZARINPAL_HASHKEY'));			

			$CpiReturnUrl = (Configuration::get('PS_SSL_ENABLED') ?'https://' :'http://').$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/zarinpalpayment/validation.php';
			
		    $params = array(
						'MerchantID' =>  $ZarinpalPin,
		                'Amount' => intval($PurchaseAmount),
						'Description' => urlencode('پرداخت سفارش شماره: '. $cart->id,
						'Email'                 => $Email,
                        'Mobile'                 => $Mobile,
						'CallbackURL' => $CpiReturnUrl
						)
		              );
			
			$res = $client->call('PaymentRequest', $params);

			$OrderId = intval($OrderId)-1;

            //--------------------------------
            if($res['Status']==100){
         	   // this is a succcessfull connection
	           //saving order information
	           setcookie("ZARINPAL_HASH", $hash, time()+1800);
	           setcookie("OrderId", $cart->id, time()+1800);
	           setcookie("PurchaseAmount", $PurchaseAmount, time()+1800);
                       
               //redirecting 
			   $ParsURL = "https://www.zarinpal.com/pg/StartPay/".$res['Authority'];
			   Tools::redirectLink($ParsURL);
			   exit() ;
			   die() ;
			   return;
     		}else{
				echo 'کد خطا: '.$res['Status'];
			}
           
        }	
        return $this->_html;
	}
	public function confirmPayment($order_amount){
        include('nusoap.php');
		$authority = $_REQUEST['Authority'];

		if ( strlen($authority)==36 ) {
			$client = new nusoap_client('https://de.zarinpal.com/pg/services/WebGate/wsdl', 'wsdl');
			$client->soap_defencoding = 'UTF-8';
			if ( (!$client) OR ($err = $client->getError()) ) {
				// this is unsucccessfull connection
				$this->_postErrors[] = $this->l($err);
				$this->displayErrors();
    		} else {
				$ZarinpalPin = Configuration::get('ZARINPAL_PIN'); 
				
				$params = array(
								'MerchantID' => $ZarinpalPin ,  // this is our PIN NUMBER
								'Authority' => $authority,
								'Amount' => intval($order_amount)
							) ; // to see if we can change it
				$res = $client->call('PaymentVerification', $params);
			}
		}
		return $res['Status'];			
	}
		
	public function showMessages($result)
	{                
		switch($result)
		{ 
			case 1:  $this->_postErrors[]=$this->l('The transaction was approved.'); break;
			case -1: $this->_postErrors[]=$this->l('Zarinpal PIN or IP Address is not correct.'); break;
			case -2: $this->_postErrors[]=$this->l('Zarinpal PIN or IP Address is not correct.'); break;
			case -11: $this->_postErrors[]=$this->l('Invalid Transaction'); break;  
			case -12: $this->_postErrors[]=$this->l('Transaction number is not correct or expired.'); break;              
		}
		$this->displayErrors();
		echo $this->_html;
		return $result;
	}	
	
	public function hookPayment($params){

		if (!$this->active)
			return ;
		
		return $this->display(__FILE__, 'payment.tpl');
	}
	
	public function hookPaymentReturn($params)
	{
		if (!$this->active)
			return ;

		return $this->display(__FILE__, 'confirmation.tpl');
	}
	/**
	 *
	 * Convert amount from a currency to an other currency automatically
	 * @param float $amount
	 * @param Currency $currency_from if null we used the default currency
	 * @param Currency $currency_to if null we used the default currency
	 */
	public static function convertPriceFull($amount, Currency $currency_from = null, Currency $currency_to = null)
	{
		if ($currency_from === $currency_to)
			return $amount;

		if ($currency_from === null)
			$currency_from = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

		if ($currency_to === null)
			$currency_to = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

		if ($currency_from->id == Configuration::get('PS_CURRENCY_DEFAULT'))
			$amount *= $currency_to->conversion_rate;
		else
		{
            $conversion_rate = ($currency_from->conversion_rate == 0 ? 1 : $currency_from->conversion_rate);
			// Convert amount to default currency (using the old currency rate)
			$amount = Tools::ps_round($amount / $conversion_rate, 2);
			// Convert to new currency
			$amount *= $currency_to->conversion_rate;
		}
		return Tools::ps_round($amount, 2);
	}
}
