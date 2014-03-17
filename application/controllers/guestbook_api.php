<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Guestbook_API extends REST_Controller
{
	function message_get()
	{
		$this->response(array('error' => 'Not implemented'), 501);
	}

	function message_post()
	{
		$this->load->model('guestbook_model');

		$method = $this->post('method');
		$key = $this->post('key');

		if ($method !== 'insert' && $key !== $this->config->item('application_id'))
		{
			$this->response(array('error' => 'Unauthorized'), 401);
		}

		switch ($method)
		{
			case 'insert':
				
				$_POST = $this->post();
				$this->formValidation();

				if ($id = $this->guestbook_model->insert($this->post()))
				{
					$this->getResult($id);
				}
				break;

			case 'update':
				
				$_POST = $this->post();
				$this->formValidation();
				
				if ($this->guestbook_model->update($this->post()))
				{
					$this->getResult($this->post('id'));
				}
				break;

			case 'approve':
				
				if ($this->guestbook_model->approve($this->post('id')))
				{
					$this->response(array('success' => 'Request completed'), 200);
				}
				break;

			case 'delete':
				
				if ($this->guestbook_model->delete($this->post('id')))
				{
					$this->response(array('success' => 'Request completed'), 200);
				}
				break;
		}

		$this->response(array('error' => 'Unknown error'), 500);
	}

	function message_put()
	{
		$this->response(array('error' => 'Not implemented'), 501);
	}

	function message_delete()
	{
		$this->response(array('error' => 'Not implemented'), 501);
	}
	
	function formValidation()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name', 'Name', 'trim|min_length[3]|max_length[20]|strip_tags|xss_clean|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|min_length[3]|max_length[255]|strip_tags|xss_clean');
		$this->form_validation->set_rules('website', 'Website', 'trim|min_length[4]|max_length[255]|strip_tags|xss_clean');
		$this->form_validation->set_rules('message', 'Message', 'trim|min_length[3]|max_length[1000]|strip_tags|xss_clean|required');
		//$this->form_validation->set_rules('recaptcha_response_field', 'Recaptcha', 'required|callback_recaptcha');
		
		if (!$this->form_validation->run())
		{
			$this->response(array('error' => 'Invalid data. Expecting fields "method: insert, update, approve or delete", "id, name, email, website, message"'), 400);
		}
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

	protected function getResult($id)
	{
		$row = $this->guestbook_model->getById($id)->row();
		
		$this->response(array(
			'success' => 'Request completed',
			'name' => $row->name,
			'email' => $row->email,
			'website' => $row->website,
			'message' => $row->message,
			'date' => $row->date,
			'id' => $row->id,
		), 200);
	}
}