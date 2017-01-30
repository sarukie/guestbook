<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// I always include a logger for logging details
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Adding the redis cache to handle captcha word identities.
use Predis\Client;

/**
 * Guestbook Controller
 */
class Guestbook extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$log = new Logger('guestbook');
		$logname = date("Y-m-j");
		$log->pushHandler(new StreamHandler(APPPATH . "/logs/{$logname}.log", Logger::WARNING));

		$this->load->helper(["form", "captcha", "url"]);
		$this->load->library(["session", "form_validation"]);
		$this->load->database();

		// Only when we're introducing the form do we need to care about creating the captcha
		if (empty($this->input->post())) {
			$formText = $this->createFormText();
		}

		$data = [];	
		$this->load->model("entry_model");

		$viewData = [
			"errors" => [],
			"entries" => [],
			"content" => null
		];

		if ($this->input->post()) {
			$this->form_validation->set_rules("Name", "Name", "required");
			$this->form_validation->set_rules("Email", "Email", "required");
			$this->form_validation->set_rules("Timezone", "Timezone", "required");
			$this->form_validation->set_rules("Comment", "Comment", "required");
			$this->form_validation->set_rules("captcha", "Captcha", "callback_captchaCheck");
			if ($this->form_validation->run() == false) {
				$log->warning("Validation errors: " . validation_errors());
				$viewData['errors'] = validation_errors("<div class=\"error\">", "</div>");
				$this->load->view("guestbook", $viewData);
			} else {
				// Clear the cache completely.
				$this->clearCacheKey();
				$this->entry_model->setName($this->input->post('Name'));
				$this->entry_model->setEmail($this->input->post('Email'));
				$this->entry_model->setTimezone($this->input->post('Timezone'));
				$this->entry_model->setComment($this->input->post('Comment'));

				try {
					$id = $this->entry_model->insert_entry();
				} catch (\Exception $exception) {
					echo "I caught an exception:" . $exception->getMessage();
					$log->error("Database exception: " . $exception->getMessage());
					redirect("/");
				}

				$this->load->model("emailworkflow_model");
				$date = new DateTime("now", new DateTimeZone($this->entry_model->getTimezone()));
				$this->emailworkflow_model->email_email = $this->entry_model->getEmail();
				$this->emailworkflow_model->email_subject = "Thank you for signing our guestbook!";
				$this->emailworkflow_model->email_message = "Welcome, you signed our guestbook on " . $date->format("l M j, Y");
				$this->emailworkflow_model->email_created = $date->format("Y-m-j H:i:s");
				$this->emailworkflow_model->email_send = date("Y-m-j H:i:s", strtotime("+5 days"));
				$this->emailworkflow_model->guestbook_id = $id;
				$this->emailworkflow_model->insert_entry();

				$viewData['content'] = "Thank you for submitting your entry<br />Click <a href=\"/\">here</a> to see your entry.";
				$this->load->view("guestbook", $viewData);
			}
		} else {
			$viewData['entries'] = $this->entry_model->get_last_ten_entries();
			$viewData['content'] = $formText;
			$this->load->view("guestbook", $viewData);
		}
	}

	private function clearCacheKey() {
		$cacheKey = $this->session->session_id . "CAPTCHA";
		$client = new Client();
		// Expires now
		$client->expire($cacheKey, 0);
	}

	public function captchaCheck($str)
	{
		$cacheKey = $this->session->session_id . "CAPTCHA";
		$client = new Client();
		$word = $client->get($cacheKey);

		if (strtolower($str) == strtolower($word)) {
			return true;
		}

		$this->form_validation->set_message("captchaCheck", "Captcha does not match.");
		return false;
	}

	public function remove()
	{
		$this->load->database();
		$id = intval($this->input->get("id"));
		$this->db->delete("guestbook", ["guestbook_id" => $id]);

		return json_encode([
			"success" => true
		]);
	}

	/**
	 * Create the captcha
	 *
	 * @return array
	 */
	private function createCaptcha()
	{
		$captchaData = [
	        'img_path'      => './captcha/',
    	    'img_url'       => 'http://guestbook.nerdcorn.com/captcha/',
    	    "pool"			=> "ABCDEFGHIJKLMNOPQRSTUVWXYZ"
		];
		$captcha = create_captcha($captchaData);

		// CodeIgniter specifies caching the captcha information in a database.
		// This seems rather resource-intensive for a volatile piece of data.
		// Instead, we're going to store it in redis.
		$client = new Client(); // No arguments means we're going to connect to localhost on default port 6379
		// We need to make this unique between sessions.
		$cacheKey = $this->session->session_id . "CAPTCHA";
		$client->set($cacheKey, $captcha['word']); // Let it expire after 1 hour
		$client->expire($cacheKey, 3600);

		return $captcha;
	}

	/**
	 * Create the form to be displayed
	 *
	 * @return string
	 */
	private function createFormText()
	{
		$captcha = $this->createCaptcha();
		$formText = form_open("guestbook");
		$formText .= "<p> Name: ".form_input([
			"name" => "Name",
			"title" => "Name",
			"placeholder" => "Enter your name here",
			"required" => "required"
		]) . "</p>";
		$formText .= "<p> Email: " . form_input([
			"name" => "Email",
			"placeholder" => "Enter your e-mail address here",
			"required" => "required"
		]) . "</p>";
		$timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
		$formText .= "<p>TimeZone: " . form_dropdown("Timezone", array_combine($timezones, $timezones), "America/Vancouver"). "</p>";
		$formText .= "<div><p>Comment:</p>" . form_textarea([
			"name" => "Comment",
			"rows" => 10,
			"cols" => 80,
			"placeholder" => "Add your comment here",
			"required" => "required"
		]) . "</div>";

		$formText .= "<div><p>Insert your Captcha Here</p>{$captcha['image']}</div>";
		$formText .= "<p>" . form_input(["name" => "captcha"]) . "</p>";

		$formText .= "<p>" . form_submit("submit", "Sign Guestbook") . "</p>";
		$formText .= form_close();

		return $formText;
	}

}
