<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include_once(dirname(__FILE__).'/zarinpalpayment.php');

	if (!$cookie->isLogged())
		Tools::redirect('authentication.php?back=order.php');
                
        $currency_default = Currency::getCurrency(intval(Configuration::get('PS_CURRENCY_DEFAULT')));        
        $zarinpalpayment= new zarinpalpayment(); // Create an object for order validation and language translations
		
		$order_cart = new Cart(intval($_COOKIE["OrderId"]));
		
		//$PurchaseAmount=number_format(Tools::convertPrice(intval($_COOKIE["PurchaseAmount"]), $currency_default), 0, '', '');
		//$OrderAmount=number_format(Tools::convertPrice($order_cart->getOrderTotal(true, 3), $currency_default), 0, '', '');

		$rial_id = Currency::getIdByIsoCode('IRR');
        $purchase_currency = new Currency($rial_id);
		$current_currency = new Currency($cookie->id_currency);
		if($cookie->id_currency == $purchase_currency->id)
			$PurchaseAmount = number_format(intval($_COOKIE["PurchaseAmount"]), 0, '', '');
		else
			$PurchaseAmount= number_format($zarinpalpayment->convertPriceFull(intval($_COOKIE["PurchaseAmount"]), $current_currency, $purchase_currency), 0, '', '');
       
	    $OrderAmount=number_format($zarinpalpayment->convertPriceFull($order_cart->getOrderTotal(true, 3), $current_currency, $purchase_currency), 0, '', '');
		
		

		
        $result = $zarinpalpayment->confirmPayment($PurchaseAmount);
	
	// We now think that the response is valid, so we can look at the result      
	$result = $zarinpalpayment->showMessages($result);

	// if we have a valid completed order, validate it

	$hash_data = 'o='.$_COOKIE["OrderId"].$_COOKIE["PurchaseAmount"];
	$hash = crypt($hash_data, Configuration::get('ZARINPAL_HASHKEY'));
	
	if (($result==100) and ($hash==$_COOKIE["ZARINPAL_HASH"]))
	{
		if($PurchaseAmount==$OrderAmount)
			 $zarinpalpayment->validateOrder(intval($_COOKIE["OrderId"]), _PS_OS_PAYMENT_,$order_cart->getOrderTotal(true, 3), $zarinpalpayment->displayName, $zarinpalpayment->l('Payment Accepted'), array(), $cookie->id_currency);
		else
			 $zarinpalpayment->validateOrder(intval($_COOKIE["OrderId"]), _PS_OS_PAYMENT_,$PurchaseAmount, $zarinpalpayment->displayName, $zarinpalpayment->l('Payment Accepted'), array(), $cookie->id_currency);


        setcookie("ZARINPAL_HASH", "", -1);
        setcookie("OrderId", "", -1);
        setcookie("PurchaseAmount","", -1);
 		
        Tools::redirect('history.php');
	}

include_once(dirname(__FILE__).'/../../footer.php');		

?>