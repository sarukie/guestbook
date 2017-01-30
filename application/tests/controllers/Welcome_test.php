<?php
/**
 * Part of ci-phpunit-test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class Guestbook_test extends TestCase
{
	public function test_index()
	{
		$output = $this->request('GET', '/');
		$this->assertContains('<h1>Welcome to Guestbook!</h1>', $output);
		$this->assertContains('<h4>Sign our Guestbook!</h4>', $output);
	}

	public function test_method_404()
	{
		$this->request('GET', 'welcome/method_not_exist');
		$this->assertResponseCode(404);
	}

	public function test_APPPATH()
	{
		$actual = realpath(APPPATH);
		$expected = realpath(__DIR__ . '/../..');
		$this->assertEquals(
			$expected,
			$actual,
			'Your APPPATH seems to be wrong. Check your $application_folder in tests/Bootstrap.php'
		);
	}

	public function test_index_post()
	{
		$output = $this->request(
			'POST',
			'/',
			[
				"name" => "Test User",
				"email" => "random.user@xyz.xyz",
				"comments" => "This is a test",
				"timezone" => "America/Vancouver",
				"captcha" => ""
			]);
		$this->assertContains("<div class=\"error\">Captcha does not match.</div>", $output);
	}
}
