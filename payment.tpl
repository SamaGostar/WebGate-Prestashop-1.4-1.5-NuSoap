<!-- Zarinpal Payment Module -->
<p class="payment_module">
    <a href="javascript:$('#zarinpalpayment_form').submit();" title="{l s='Pay by Zarinpal' mod='zarinpalpayment'}">
        <img src="modules/zarinpalpayment/zarinpal.png" alt="{l s='Pay by Zarinpal' mod='zarinpalpayment'}" />
		{l s='Pay by Debit/Credit card through Zarinpal Online Merchent.' mod='zarinpalpayment'}
<br>
</a></p>
<form action="modules/zarinpalpayment/payment.php" method="post" id="zarinpalpayment_form" class="hidden">
    <input type="hidden" name="orderId" value="{$orderId}" />
</form>
<br><br>
<!-- End of Zarinpal Payment Module-->
