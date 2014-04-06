<p class="payment_module">
    <a onClick="$('.zarinpalwg').submit();" title="{l s='Online payment with zarinpalwg' mod='zarinpalwg'}">
        <img src="modules/zarinpalwg/logo.png" alt="{l s='Online payment with zarinpalwg' mod='zarinpalwg'}" />
		{l s='Online payment with zarinpalwg' mod='zarinpalwg'}
</a>
{$zarinpalwg_logo}
</p>
<a class="exclusive_large" onClick="$('.zarinpalwg').submit();" title="{l s='Online payment with zarinpalwg' mod='zarinpalwg'}">{l s='Online payment with zarinpalwg' mod='zarinpalwg'}</a>
<form action="modules/zarinpalwg/zp.php?do=payment" method="post" class="zarinpalwg">
    <input type="hidden" name="id" value="{$orderId}" />
</form>