<p class="message"><?php echo lang('message_confirm_delete') ?></p>

<blockquote>
	<cite><?php echo $message->name ?></cite>
	<div><p><?php echo $message->message ?></p></div>
</blockquote>

<form action="<?php echo site_url('delete/' . $message->id) ?>" method="post" accept-charset="utf-8">
<input type="submit" name="confirm" value="<?php echo lang('form_submit_delete') ?>" />
</form>