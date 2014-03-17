<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller
{
	protected $view;

	function __construct()
	{
		parent::__construct();

		$this->load->language('user');
		$this->view = $this->router->method;
	}

	function login()
	{
		$this->doesViewExist();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('email', 'Email', 'trim|min_length[3]|max_length[255]|strip_tags|xss_clean|required');
		$this->form_validation->set_rules('password', 'Password', 'max_length[255]|required');

		if ($this->form_validation->run() === true)
		{
			if ($this->user_model->login($this->input->post()))
			{
				redirect('/', 'location');
			}

			$this->session->set_flashdata('gbMessage', 'user_login_error');

			redirect('/user/login', 'location');
		}

		$this->render();
	}

	function logout()
	{
		$this->user_model->logout();

		redirect('/', 'location');
	}

	protected function doesViewExist()
	{
		if (!file_exists('application/views/user/' . $this->view . '.php'))
		{
			show_404();
		}
	}

	protected function render($data = null)
	{
		$this->load->view('templates/guestbook_header', array(
			'title' => 'page_title_user_' . $this->router->method,
		));

		$this->load->view('user/' . $this->view, $data);
		$this->load->view('templates/guestbook_footer');
	}
}