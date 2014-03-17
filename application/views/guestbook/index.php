<?php if (!empty($showApproveAll)) { ?>
<a href="<?php echo site_url('approve_all') ?>" id="approveAll" class="button"><?php echo lang('message_approve_all') ?></a>
<?php } ?>
<div role="presentation" tabindex="-1" id="data" data-baseurl="<?php echo site_url('/') ?>"<?php if ($this->session->userdata('authUser')) { ?> data-key="<?php echo $this->config->item('application_id') ?>"<?php } ?>></div>
<div role="presentation" tabindex="-1" id="lang" data-langsuccess="<?php echo lang('ajax_generic_success') ?>" data-langerror="<?php echo lang('ajax_generic_error') ?>" data-langinsert="<?php echo lang('message_insert_pending') ?>" data-langdelete="<?php echo lang('message_confirm_delete') ?>"></div>
<div role="presentation" tabindex="-1" id="success"></div>
<div role="presentation" tabindex="-1" id="error"></div>
<?php

if ($messages->num_rows == 0) { ?><p class="message">No messages.</p><?php }

else {

foreach ($messages->result() as $msg) { ?>

<blockquote data-messageid="<?php echo $msg->id ?>">

	<cite><?php if (!empty($msg->website)) { ?><a href="<?php echo $msg->website ?>"><?php echo $msg->name ?></a><?php } else { echo $msg->name; } ?></cite>
	<?php if ($this->session->userdata('authUser') && !empty($msg->email)) { ?> <a href="mailto:<?php echo $msg->email ?>" class="email"><?php echo $msg->email ?></a>
	<?php } ?><time><?php echo $msg->date ?></time>
	<div><?php echo $msg->message ?></div>
	<?php if ($this->session->userdata('authUser')) { ?>
	
	<span class="controls">
		<?php if ($msg->status != 1) { ?><a href="<?php echo site_url('approve/' . $msg->id) ?>" class="approveMessage"><span><?php echo lang('message_approve') ?></span></a>
		<?php } ?><a href="<?php echo site_url('edit/' . $msg->id) ?>" class="updateMessage"><span><?php echo lang('message_update') ?></span></a>
		<a href="<?php echo site_url('delete/' . $msg->id) ?>" class="deleteMessage"><span><?php echo lang('message_delete') ?></span></a>
	</span><?php } ?>

</blockquote>
<?php }
}

echo $this->pagination->create_links() ?>