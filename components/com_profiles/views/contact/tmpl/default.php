<?php
$doc = JFactory::getDocument();
$doc->addStyleSheet('/components/com_profiles/assets/css/contact.css');
$sanity_check = rand();
if($this->request_sent)
{
?>

<script type="text/javascript">
function closeFancyBox()
{
	parent.jQuery.fancybox.close();
}
</script>

<div class="success">
	Your request has been sent. <a href="javascript:closeFancyBox();">Close and return to profile.</a>
</div>

<?php
}
else
{
?>
	<div id="header"></div>
	<div class="form_wrapper" id="contact_form">
		<h1><?php echo $this->family->first_name; echo $this->family->spouse_name ? ' &amp; ' . $this->family->spouse_name : '';?></h1>
	    <form action="/index.php" method="post" name="contact_form">
	        <ul>
	            <li>
	            	<label>Name:</label>
	            	<input class="required" type="text" name="jform[name]" value="" />
	            </li>
	            <li>
	            	<label>Email:</label>
	            	<input class="required email" type="text" name="jform[email]" value="" />
	            </li>
	            <li>
	            	<label>Phone:</label>
	            	<input class="required phone" type="text" name="jform[phone]" value="" />
	            </li>
	            <li>
	            	<label>Race of your baby:</label>
	            	<select name="jform[baby_race]"><option value="Please Select">Race of your baby:</option><option value="African American">African American</option><option value="Asian">Asian</option><option value="Caucasian">Caucasian</option><option value="Caucasian / African American">Caucasian / African American</option><option value="Caucasian / Asaian">Caucasian / Asaian</option><option value="Caucasian / Hispanic">Caucasian / Hispanic</option><option value="Hispanic">Hispanic</option><option value="Hispanic / African American">Hispanic / African American</option><option value="Pacific Islander">Pacific Islander</option><option value="Other">Other</option></select><br />
	            </li>
	            <li>
	            	<label>Due Date:</label>
	            	<input class="required due_date" type="text" name="jform[due_date]" value="" />
	           	</li>
	            <li>
	            	<label>Message:</label>
	            	<textarea name="jform[message]" cols="30" rows="5"></textarea>
	            </li>
	            <li style="text-align:center">
	            	<input type="submit" id="submit" value="" />
	            </li>
			</ul>
	        <input id="spmck" type="text" name="jform[sanity_check]" value="<?php echo $sanity_check ?>" />
	        <input type="hidden" name="id" value="<?php echo $this->family->id; ?>" />
	        <input type="hidden" name="jform[check]" value="<?php echo $sanity_check ?>" />
	        <input type="hidden" name="option" value="com_profiles" />
	        <input type="hidden" name="task" value="send_contact" />
	    </form>
	</div>
<?php
}
?>