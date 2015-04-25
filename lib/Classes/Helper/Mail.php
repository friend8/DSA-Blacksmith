<?php
/**
 * Part of the dsa blacksmith
 *
 * @package Helper
 * @author  friend8 <map@wafriv.de>
 * @license https://www.gnu.org/licenses/lgpl.html LGPLv3
 */
namespace Helper;

/**
 * Helper class for handling mails.
 *
 * @package Helper
 * @author  friend8 <map@wafriv.de>
 * @license https://www.gnu.org/licenses/lgpl.html LGPLv3
 */
class Mail
{
	/**
	 * Send an email
	 *
	 * @param array  $recipient The recipient to send the mail to
	 *                          Format:
	 *                          array(
	 *                              'test@example.org',
	 *                              'test',
	 *                          )
	 * @param string $subject   The mail subjet
	 * @param string $message   The mail message, may be html
	 *
	 * @return boolean
	 */
	public static function send($recipient, $subject, $message)
	{
		$translator = \SmartWork\Translator::getInstance();

		$mailer = new \PHPMailer(true);
		$mailer->set('CharSet', $GLOBALS['config']['charset']);
		$mailer->setFrom($GLOBALS['mail']['sender'], $translator->getTranslation('title'));
		$mailer->addAddress($recipient[0], $recipient[1]);
		$mailer->set('Subject', $subject);
		$mailer->set('AltBody', strip_tags($message));
		$mailer->msgHTML($message);
		$mailer->isHTML(true);

		return $mailer->send();
	}
}