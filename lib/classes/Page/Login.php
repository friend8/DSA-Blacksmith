<?php
namespace Page;

/**
 * Description of Login
 *
 * @author Neithan
 */
class Login extends \SmartWork\Page
{
	function __construct()
	{
		parent::__construct('login');
	}

	public function process()
	{
		$this->logIn($_POST['username'], $_POST['password'], $_POST['login']);
	}

	protected function logIn($username, $password, $salt)
	{
		if (!$salt || $salt != $_SESSION['formSalts']['login'])
			return;

		if (!$username && !$password)
		{
			$this->template->assign('error', 'emptyLogin');
			return;
		}

		$user = \SmartWork\User::getUser($username, $password);

		if ($user)
		{
			$_SESSION['userId'] = $user->getUserId();
			$translator = \SmartWork\Translator::getInstance();
			$translator->setCurrentLanguage($user->getLanguageId());
			redirect('index.php?page=Index');
		}
		else
			$this->template->assign('error', 'invalidLogin');
	}
}
