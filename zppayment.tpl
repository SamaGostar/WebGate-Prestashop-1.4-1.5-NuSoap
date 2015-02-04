
<!-- Zarinpal Payment Module -->
<p class="payment_module">
    <a href="javascript:$('#zarinpalwg').submit();" title="{l s='Pay by Zarinpal' mod='zarinpalwg'}">
        <img src="modules/zarinpalwg/zarinpal.png" alt="{l s='Pay by Zarinpal' mod='zarinpalwg'}" />
		{l s='Pay by Debit/Credit card through Zarinpal Online Merchent.' mod='zarinpalwg'}
<br>
</a></p>

<form action="modules/zarinpalwg/payment.php" method="post" id="zarinpalwg" class="hidden">
    <input type="hidden" name="orderId" value="{$orderId}" />
</form>
<br><br>
<!-- End of Zarinpal Payment Module-->
