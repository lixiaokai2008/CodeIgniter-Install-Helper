<?php
$this->load->view('install/install_header');
?>

<h2><a id="top"></a><?php echo $this->lang->line('logo');?> <?php echo $page_title;?></h2>

<h3><?php echo $this->lang->line('install_welcome');?></h3>

<?php echo form_open('install', array('id'=>'loginform'));?>

<p><?php echo $this->lang->line('install_explain');?></p>



<p>
	<?php echo form_label('<span>'.$this->lang->line('login_username').'</span>', 'login_username');?> 
	<?php echo form_input('login_username');?>
</p>

<p>
	<?php echo form_label('<span>'.$this->lang->line('settings_primary_contact').'</span>', 'primary_contact');?> 
	<?php echo form_input('primary_contact');?>
</p>

<p>
	<?php echo form_label('<span>'.$this->lang->line('login_password').'</span>', 'login_password');?> 
	<?php echo form_password('login_password');?>
</p>

<p>
	<?php echo form_label('<span>'.$this->lang->line('login_password_confirm').'</span>', 'login_password_confirm');?> 
	<?php echo form_password('login_password_confirm');?>
</p>

<p>
	<?php echo form_submit('login_credentials', $page_title);?>
</p>

<?php echo form_close();?>

<?php
$this->load->view('install/install_footer');
?>