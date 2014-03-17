<form action="<?php echo site_url('user/login') ?>" method="post" accept-charset="utf-8">
<?php echo validation_errors() ?>
<label><?php echo lang('form_email') ?> <input type="email" name="email" value="<?php echo set_value('email') ?>" required autofocus placeholder="<?php echo lang('form_email') ?>" /></label>
<label><?php echo lang('form_password') ?> <input type="password" name="password" required placeholder="<?php echo lang('form_password') ?>" /></label>
<input type="submit" value="<?php echo lang('form_submit') ?>" />
</form>