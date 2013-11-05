<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include_once(dirname(__FILE__).'/zarinpalpayment.php');

if (!$cookie->isLogged())
    Tools::redirect('authentication.php?back=order.php');
	
$zarinpalpayment= new zarinpalpayment();
echo $zarinpalpayment->execPayment($cart);

include_once(dirname(__FILE__).'/../../footer.php');

?>
