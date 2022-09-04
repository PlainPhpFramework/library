<?php

namespace pp;

use Symfony\Component\Mailer\Mailer as sfMailer;
use Symfony\Component\Mime\Email;
use Pelago\Emogrifier\CssInliner;

/**
 * Usage:
 * 
 * use pp\Mailer;
 * use Symfony\Component\Mailer\Transport;
 * use Symfony\Component\Mailer\Mailer AS SymfonyMailer;
 * use Symfony\Component\Mime\Email;
 * 
 * $mailer = new SymfonyMailer(Transport::fromDsn($dsn));
 * $mailer = new class ($mailer, $baseUrl, $wwwPath) extends Mailer
 * {
 * 
 * 		function newEmail(): Email
 * 		{
 * 			return (new Email)->from('me@example.com')->sender('me@example.com');
 * 		}
 * 	
 * }
 * 
 * // Send a message rendering a view
 * $mailer->send('path/to/email_message.php', ['order' => $order], 
 * 		subject: 'Welcome!', 
 * 		to: $user->email);
 * 
 * // An alternative that allow replacing parameters in message
 * $email = $mailer->compileMessage('path/to/email_message.php', ['order' => $order]);
 * $html = $email->getHtmlBody();
 * 
 * $html = str_replace('{user}', $user->name, $html);
 * 
 * $email->subject('Welcome!')
 * 		->to($user->email)
 * 		->html($html); 
 * 
 * $mailer->mailer->send($email);
 * 
 * // Send a basic message
 * $mailer->send(
 * 		to: $user->email, 
 * 		subject: 'Welcome', 
 * 		html: '<p>Welcome!</p>', 
 * 		text: 'Welcome!');
 * 
 * // Basic message the long way
 * $email = $mailer->newEmail();
 * 
 * $email->to($user->email)
 * 		->subject('Welcome')
 * 		->text('Welcome!')
 * 		->html('<p>Welcome!</p>');
 * 
 * $mailer->mailer->send($email);
 */


abstract class Mailer 
{

	function __construct(
		public sfMailer $mailer,
		public string $baseUrl = 'http://example.com/',
		public string $basePath = '/path/to/www',
	) {}

	abstract function newEmail(): Email;

	function compileMessage($message, array $context = null) 
	{
		

		$email = $this->newEmail();

        // Render the message view
		extract($context, EXTR_SKIP);
		ob_start();
		require $message;
		$message = ob_get_clean();

        // Inline the CSS
		$message = CssInliner::fromHtml($message)->inlineCss()->render();

        // Embed images
		$base = rtrim($this->baseUrl, '/') . '/';
		$www = rtrim($this->basePath, '/') . '/';

		if (preg_match_all('#(<[^>]+(?:src|background))=["\']('.preg_quote($base).'.*)["\']#Ui', $message, $images)) {
			
			$cids = [];
			foreach ($images[2] as $index => $url) {

				$cid = str_replace([$base, '?'.parse_url($url, PHP_URL_QUERY)], '', $url);
				$path = $www . $cid;

				if (!isset($cids[$cid])) {
					$cids[$cid] = true;
					$email->attachFromPath($path, $cid);
				}

				$message = str_replace(
					$images[0][$index], 
					$images[1][$index]."='cid:$cid'",
					$message);

			}

		}

		$email->html($message);

		return $email;

	}

	function send(
		string $message = null, 
		array $context = null, 
		string $subject = null, 
		string|array $to  = null,
		string $html = null, 
		string $text = null,
		string|array $cc = null,
		string|array $bcc = null,
		string|array $replyTo = null,
		string|array $from = null,
		string|array $sender = null,
		string|array $attach = null
	) 
	{

		$email = ($message && is_string($message))? 
		$this->compileMessage($message, $context):
		$this->newEmail()
		;

		$to && $email->to(...(array) $to);
		$cc && $email->cc(...(array) $cc);
		$bcc && $email->bcc(...(array) $bcc);
		$replyTo && $email->replyTo(...(array) $replyTo);
		$from && $email->from(...(array) $from);
		$sender && $email->sender(...(array) $sender);

		$subject && $email->subject($subject);
		$html && $email->html($html);
		$text && $email->text($text);

		$attach && array_map([$email, 'attachFromPath'], (array) $attach);

		$this->mailer->send($email);

	}

}