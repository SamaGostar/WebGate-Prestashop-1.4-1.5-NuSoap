
<!-- Zarinpal Payment Module -->
<p class="payment_module">
    <a href="javascript:$('#zarinpalzg').submit();" title="{l s='Online payment with zarinpalzg' mod='zarinpalzg'}">
        <img src="modules/zarinpalzg/zarinpal.png" alt="{l s='Online payment with zarinpalzg' mod='zarinpalzg'}" />
		{l s=' پرداخت با کارتهای اعتباری / نقدی بانک های عضو شتاب توسط دروازه پرداخت بانک سامان  ' mod='zarinpalzg'}
<br>
</a></p>

<form action="modules/zarinpalzg/zp.php?do=payment" method="post" id="zarinpalzg" class="hidden">
    <input type="hidden" name="orderId" value="{$orderId}" />
</form>
<br><br>
<!-- End of Zarinpal Payment Module-->
