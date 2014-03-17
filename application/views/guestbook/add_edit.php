<form action="<?php echo site_url($this->router->method . '/' . $message['id']) ?>" method="post" accept-charset="utf-8" id="messageForm">
<?php echo validation_errors() ?>
<label><?php echo lang('form_name') ?> <input type="text" name="name" value="<?php echo set_value('name', $message['name']) ?>" maxlength="20" required autofocus placeholder="<?php echo lang('form_name') ?>" /></label>
<label><?php echo lang('form_email') ?> <input type="email" name="email" value="<?php echo set_value('email', $message['email']) ?>" placeholder="<?php echo lang('form_email') ?>" /></label>
<label><?php echo lang('form_website') ?> <input type="url" name="website" value="<?php echo set_value('website', $message['website']) ?>" pattern=".+\..+" placeholder="<?php echo lang('form_website') ?>" /></label>
<label><?php echo lang('form_message') ?> <textarea name="message" required placeholder="<?php echo lang('form_message') ?>"><?php echo set_value('message', str_replace(array('</p><p>', '<br />', '<p>', '</p>'), array("\n\n", "\n", "", ""), $message['message'])) ?></textarea></label>
<input type="hidden" name="id" value="<?php echo set_value('id', $message['id']) ?>" />
<script>
	var RecaptchaOptions = {
		theme: 'white'
	};
</script>
<script src="http://www.google.com/recaptcha/api/challenge?k=YOUR_PUBLIC_RECAPTCHA_KEY"></script>
<input type="submit" value="<?php echo lang('form_submit') ?>" />
</form>