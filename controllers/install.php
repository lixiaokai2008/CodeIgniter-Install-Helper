<?php

// This install file should be deleted after installation is complete.


class Install extends CI_Controller {
		
	function __construct() {
		//session_start();
		parent::__construct();		
		$this->load->library('encrypt');
		$this->load->dbutil();
	}

	// --------------------------------------------------------------------

	function index() {
		if ( $this->db->table_exists('users') ) {
			// Hopefully the script dies here if a previous install exists. You should still delete this file after install has completed though.
		}

		$this->load->helper('url');
		$this->load->helper('form', 'url');
		$this->load->library('form_validation');
		$this->load->language('installer');
		$this->form_validation->set_rules('login_username', 'Username', 'required|valid_email');
		$this->form_validation->set_rules('login_password', 'Password', 'required|matches[login_password_confirm');
		$this->form_validation->set_rules('login_password_confirm', 'Confirm Password', 'required');
		$this->form_validation->set_rules('primary_contact', 'Primary Contact', 'required');
		$fields['login_username']	= $this->lang->line('login_username');
		$fields['login_password']	= $this->lang->line('login_password');
		$fields['login_password_confirm'] = $this->lang->line('login_password_confirm');
		$fields['primary_contact']	= $this->lang->line('settings_primary_contact');
		$this->form_validation->set_rules($fields);

		if ($this->form_validation->run() == FALSE) {
			$vars['message'] = '';
			$vars['page_title'] = $this->lang->line('install_install');
			$this->load->view('install/index', $vars);
		} else {
			$email = $this->input->post('login_username');
			$password = $this->input->post('login_password');
			$primary_contact = $this->input->post('primary_contact');
			$this->do_install($email, $password, $primary_contact);
		}
	}

	// --------------------------------------------------------------------

	function do_install($admin_email = '', $admin_password = '', $primary_contact = '') {
		
		if ( ! isset($admin_password) || !isset($admin_email)) {
			show_error("Please set your admin login, email and password. After you have done this, you can try re-installing.");
		}

		if ($admin_email == '' OR $admin_password == '' OR $primary_contact == '') {
			show_error('something went wrong... no username or password');
		}

		$this->load->dbforge();

		// sessions_table
		$sessions_definition = array(
									'session_id'  => array('type' => 'VARCHAR', 'constraint' => 40, 'default' => 0),
									'ip_address'  => array('type' => 'VARCHAR', 'constraint' => 16, 'default' => 0),
									'user_agent'  => array('type' => 'VARCHAR', 'constraint' => 50, 'default' => ''),
									'last_activity' => array('type' => 'INT', 'constraint' => 10, 'unsigned' => TRUE, 'default' => 0),
									'user_id'  => array('type' =>'INT', 'constraint' => 50, 'default' => 0),
									'session_data' => array('type' =>'TEXT'),
									'logged_in'  => array('type' => 'INT', 'constraint' => 1, 'default' => 0)
									);

		$this->dbforge->add_field($sessions_definition);
		$this->dbforge->add_key('session_id', TRUE);
		$this->dbforge->add_key('ip_address', TRUE);
		$this->dbforge->create_table('sessions', TRUE);

		$users_definition = array(
									'id'  => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE),
									'first_name'  => array('type' => 'VARCHAR', 'constraint' => 25),
									'last_name'  => array('type' => 'VARCHAR', 'constraint' => 25),
									'email'  => array('type' => 'VARCHAR', 'constraint' => 50),
									'password'  => array('type' => 'VARCHAR', 'constraint' => 100),
									'admin'  => array('type' => 'INT', 'constraint' => 11),
									'last_login'  => array('type' => 'INT', 'constraint' => 11),
									'password_reset' => array('type' => 'VARCHAR', 'constraint' => 12),
									'optin' => array('type' => 'INT', 'constraint' => 11)
									);

		$this->dbforge->add_field($users_definition);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('users', TRUE);
		
		$points = array(
									'userID'  => array('type' => 'INT', 'constraint' => 11),
									'brandID'  => array('type' => 'INT', 'constraint' => 11),
									'totalPoints'  => array('type' => 'INT', 'constraint' => 11)
									);

		$this->dbforge->add_field($points);
		$this->dbforge->create_table('points', TRUE);

		$points_definition = array(
									'task'  => array('type' => 'VARCHAR', 'constraint' => 50),
									'pointsAwarded'	=> array('type' => 'INT', 'constraint' => 11),
									'taskDescription'  => array('type' => 'VARCHAR', 'constraint' => 255)
									);

		$this->dbforge->add_field($points_definition);
		$this->dbforge->create_table('points', TRUE);

		
		// Insert some starting data, username and password
		$this->db->set('id');
		$this->db->set('email', $admin_email);
		$this->db->set('password', $this->encrypt->encode($admin_password));
		$this->db->set('last_login', time());
		$this->db->set('admin', 1);
		$this->db->insert('users');

		// great success
		redirect('welcome', 'refresh');
	}

	// --------------------------------------------------------------------

	function not_installed() {
		$this->load->helper('url');
		echo $this->load->view('install/install_header', '', TRUE);
		echo 'Framework does not appear to be installed. '.anchor ('install', 'Ready to install').'.';
		echo $this->load->view('install/install_footer', '', TRUE);
	}

	// --------------------------------------------------------------------

}
?>