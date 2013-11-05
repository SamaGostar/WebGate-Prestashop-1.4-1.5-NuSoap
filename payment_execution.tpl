{capture name=path}{l s='Credit/Debit Card' mod='zarinpalpayment'}{/capture}
{include file=$tpl_dir./breadcrumb.tpl}
<h2>{l s='Order summary' mod='zarinpalpayment'}</h2>

{assign var='current_step' value='payment'}
{include file=$tpl_dir./order-steps.tpl}

{if $nbProducts <= 0}
	<p class="warning">{l s='Your shopping cart is empty.' mod='zarinpalpayment'}</p>
{else}
<h3>{l s='Credit Card Payment' mod='zarinpalpayment'}</h3>
<p><img src="{$this_path}zarinpal.png" alt="{l s='Zarinpal' mod='zarinpalpayment'}" style="float:left; margin: 0px 10px 5px 0px;" />
	{l s='You have chosen to pay by credit or debit card through Zarinpal.' mod='zarinpalpayment'}</p>
<br />
<p>{l s=' We accept the following currency via this method:' mod='zarinpalpayment'}&nbsp;<b>{l s=' IRR' mod='zarinpalpayment'}</b></p>
<br />
<h3>{l s='Order Details:' mod='zarinpalpayment'}</h3>
<p style="margin-top:20px;">
	{l s='The amount to be debited from your selected card is:' mod='zarinpalpayment'}
	<span id="amount" class="price">{$TotalAmount}</span>
</p>
    <br />
    {if $invoice->id}
	<div>
    <ul class="address" style="width: 100%">
    	<li class="address_title">{l s='Registered Billing Address'}</li>
		{if $invoice->company}<li class="address_company">{$invoice->company|escape:'htmlall':'UTF-8'}</li>{/if}
		<li class="address_name">{$invoice->lastname|escape:'htmlall':'UTF-8'} {$invoice->firstname|escape:'htmlall':'UTF-8'}</li>
		<li class="address_address1">{$invoice->address1|escape:'htmlall':'UTF-8'}</li>
		{if $invoice->address2}<li class="address_address2">{$invoice->address2|escape:'htmlall':'UTF-8'}</li>{/if}
		<li class="address_city">{$invoice->postcode|escape:'htmlall':'UTF-8'} {$invoice->city|escape:'htmlall':'UTF-8'}</li>
		<li class="address_country">{$invoice->country|escape:'htmlall':'UTF-8'}</li>
	</ul>
    </div>
    {/if}
<br /><br />
<p>
	<b>{l s='Please confirm your order by clicking \'I confirm my order\'' mod='zarinpalpayment'}.</b>
</p>

<!-- Please note if any of the variables are changed in zarinpalpayment.php they also have to defined below -->     
<form name="checkout_confirmation" action="{$form_url}" method="post">
	<p class="cart_navigation">
		<a href="{$base_dir_ssl}order.php?step=3" class="button_large">{l s='Other payment methods' mod='zarinpalpayment'}</a>
			<input type="submit" name="submit" value="{l s='I confirm my order' mod='zarinpalpayment'}" class="exclusive_large" />
	</p>
</form>  
{/if}