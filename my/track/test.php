<html>
<head>
</head>
Going to the park.
<?php if (!empty($_GET)) { 
echo "<pre>";
var_dump($_GET);
echo "</pre>";
}
?>
<script src="//my.digitalaltitude.co/track/pixel.js?v=<?php echo rand(1,1000000);?>"></script>
<form method="get">
<script src="//my.digitalaltitude.co/track/cf.js?v=<?php echo rand(1,1000000);?>"></script>
<input type='submit' value='submit'>
</form>


<form method="get">
<input id="cf_contact_shipping_city" name="contact[shipping_city]" data-cf-form-field="shipping_city" placeholder="shipping_city" data-stripe="shipping_city">
<input id="cf_contact_shipping_state" name="contact[shipping_state]" data-cf-form-field="shipping_state" placeholder="shipping_state" data-stripe="shipping_state">
	<script src="//my.digitalaltitude.co/track/cf_fields.js?v=<?php echo rand(1,1000000);?>"></script>
<input type='submit' value='submit'>
</form>
</html>