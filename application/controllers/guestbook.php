<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Guestbook extends CI_Controller
{
	protected $view;

	function __construct()
	{
		parent::__construct();

		$this->load->model('guestbook_model');
		$this->view = $this->router->method;
	}

	function index()
	{
		$this->doesViewExist();
		$this->load->library('pagination');

		$page = $this->uri->uri_string > 1 ? (int) $this->uri->uri_string : 1;
		$limit = 5;

		$data = $this->guestbook_model->select(array(
			'status' => ($this->session->userdata('authUser') ? 2 : 1),
			'offset' => --$page * $limit,
			'limit' => $limit,
		));

		$this->pagination->initialize(array(
			'total_rows' => $this->guestbook_model->count(array(
				'status' => ($this->session->userdata('authUser') ? 2 : 1),
			)),
			'per_page' => $limit,
		));

		$this->render(array(
			'messages' => $data,
			'showApproveAll' => $this->session->userdata('authUser') && $this->guestbook_model->unapprovedCount() ? true : false,
		));
	}

	function edit($id)
	{
		$this->view = 'add_edit';
		$this->doesViewExist();

		if (!$this->session->userdata('authUser'))
		{
			redirect('/', 'location');
		}

		$this->setFormValidation();
		
		if ($this->form_validation->run())
		{
			if ($this->guestbook_model->update($this->input->post()))
			{
				$this->session->set_flashdata('gbMessage', 'message_update_success');

				redirect('/', 'location');
			}
			//$this->session->set_flashdata('gbMessage', 'message_update_error');
		}

		$data = $this->guestbook_model->getById($id)->row_array();

		$this->render(array(
			'message' => $data,
		));
	}

	function add()
	{
		$this->view = 'add_edit';
		$this->doesViewExist();
		$this->setFormValidation();
		
		if ($this->form_validation->run())
		{
			if ($this->guestbook_model->insert($this->input->post()))
			{
				if (!$this->session->userdata('authUser'))
				{
					$this->session->set_flashdata('gbMessage', 'message_insert_pending');
				}

				redirect('/', 'location');
			}
			//$this->session->set_flashdata('gbMessage', 'message_insert_error');
		}

		$this->render(array(
			'message' => array(
				'id' => null,
				'name' => $this->session->userdata('authUser') ? $this->session->userdata('authUser')->name : null,
				'email' => $this->session->userdata('authUser') ? $this->session->userdata('authUser')->email : null,
				'website' => $this->session->userdata('authUser') ? $this->session->userdata('authUser')->website : null,
				'message' => null,
			),
		));
	}

	function delete($id)
	{
		$this->doesViewExist();

		if ($this->input->post())
		{
			if ($this->guestbook_model->delete($id))
			{
				$this->session->set_flashdata('gbMessage', 'message_delete_success');

				redirect('/', 'location');
			}
			//$this->session->set_flashdata('gbMessage', 'message_delete_error');
		}

		$this->render(array(
			'message' => $this->guestbook_model->getById($id)->row(),
		));
	}

	function approve($id)
	{
		if ($this->session->userdata('authUser') && !$this->guestbook_model->approve($id))
		{
			$this->session->set_flashdata('gbMessage', 'message_approve_error');
		}

		redirect('/', 'location');
	}

	function approve_all()
	{
		if ($this->session->userdata('authUser') && !$this->guestbook_model->approveAll())
		{
			$this->session->set_flashdata('gbMessage', 'message_approve_error');
		}

		redirect('/', 'location');
	}

	protected function doesViewExist()
	{
		if (!file_exists('application/views/guestbook/' . $this->view . '.php'))
		{
			show_404();
		}
	}
	
	function setFormValidation()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name', 'Name', 'trim|min_length[3]|max_length[20]|strip_tags|xss_clean|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|min_length[3]|max_length[255]|strip_tags|xss_clean');
		$this->form_validation->set_rules('website', 'Website', 'trim|min_length[4]|max_length[255]|strip_tags|xss_clean');
		$this->form_validation->set_rules('message', 'Message', 'trim|min_length[3]|max_length[1000]|strip_tags|xss_clean|required');
		$this->form_validation->set_rules('recaptcha_response_field', 'Recaptcha', 'required|callback_recaptcha');
	}

	/**
	 * @return boolean True if captcha is correct, false on error
	 */
	function recaptcha()
	{
		$this->load->library('recaptchalib');
		$this->recaptchalib->checkAnswer($this->input->post('recaptcha_challenge_field'), $this->input->post('recaptcha_response_field'));

		if ($this->recaptchalib->isValid())
		{
			return true;
		}
		else
		{
			$this->form_validation->set_message('recaptcha', 'Your input did not match the image. Please try again.');

			return false;
		}
	}
	
	/**
	 * @param array $data Data to be sent to view
	 */
	protected function render($data = null)
	{
		$this->load->view('templates/guestbook_header', array(
			'title' => 'page_title_' . $this->router->method,
		));

		$this->load->view('guestbook/' . $this->view, $data);

		//@TODO this is repetition
		if ($this->router->method === 'index')
		{
			$this->setFormValidation();

			$this->load->view('guestbook/add_edit.php', array(
				'id' => null,
				'name' => $this->session->userdata('authUser') ? $this->session->userdata('authUser')->name : null,
				'email' => $this->session->userdata('authUser') ? $this->session->userdata('authUser')->email : null,
				'website' => $this->session->userdata('authUser') ? $this->session->userdata('authUser')->website : null,
				'message' => null,
			));
		}

		$this->load->view('templates/guestbook_footer');
	}
}