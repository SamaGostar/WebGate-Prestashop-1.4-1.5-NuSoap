
<!-- Zarinpal Payment Module -->
<p class="payment_module">
    <a href="javascript:$('#zarinpalwg').submit();" title="{l s='Online payment with zarinpalwg' mod='zarinpalwg'}">
        <img src="modules/zarinpalwg/zarinpal.png" alt="{l s='Online payment with zarinpalwg' mod='zarinpalwg'}" />
		{l s=' پرداخت با کارتهای اعتباری / نقدی بانک های عضو شتاب توسط دروازه پرداخت زرين پال ' mod='zarinpalwg'}
<br>
</a></p>

<form action="modules/zarinpalwg/zp.php?do=payment" method="post" id="zarinpalwg" class="hidden">
    <input type="hidden" name="orderId" value="{$orderId}" />
</form>
<br><br>
<!-- End of Zarinpal Payment Module-->
