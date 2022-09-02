<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

use pp\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer AS SymfonyMailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Part\DataPart;

final class MailerTest extends TestCase
{

    static Mailer $mailer;
    static Email $email;

    public function setUp(): void
    {

        self::$email = new Email;

        $mailer = new SymfonyMailer(Transport::fromDsn('null://null'));
        self::$mailer = new class ($mailer, 'https://example.com/', __dir__) extends Mailer 
        {

            function newEmail(): Email
            {
                return MailerTest::$email;
            }

        };

    }

    public function testSendParamsProxyToEmailObject(): void
    {
        $address = [Address::fromString('Me <me@example.com>')];
        $subject = 'Test subject';
        $html = '<p>Test html</p>';
        $text = 'Test subject';

        self::$mailer->send(
            from: $address,
            to: $address,
            cc: $address,
            bcc: $address,
            replyTo: $address,
            sender: $address,
            subject: $subject,
            html: $html,
            text: $text,
            attach: __dir__.'/assets/logo.email.png'
        );

        $this->assertEquals(
            $address,
            self::$email->getFrom()
        );

        $this->assertEquals(
            $address,
            self::$email->getTo()
        );

        $this->assertEquals(
            $address,
            self::$email->getCc()
        );

        $this->assertEquals(
            $address,
            self::$email->getBcc()
        );

        $this->assertEquals(
            $address,
            self::$email->getReplyTo()
        );

        $this->assertEquals(
            $address[0],
            self::$email->getSender()
        );

        $this->assertEquals(
            $subject,
            self::$email->getSubject()
        );

        $this->assertEquals(
            $html,
            self::$email->getHtmlBody()
        );

        $this->assertEquals(
            $text,
            self::$email->getTextBody()
        );

        $this->assertEquals(
            'image',
            self::$email->getAttachments()[0]->getMediaType()
        );

        $this->assertSame(
            1,
            count(self::$email->getAttachments())
        );


    }

    /**
     * @depends testCompileMessage
     */

    public function testSendUsingViewMessage(string $compiledHtml): void
    {

        $address = 'Me <me@example.com>';

        self::$mailer->send(
            __dir__.'/assets/email.php',
            ['user' => 'Test User'],
            from: $address,
            to: $address
        );

        $html = self::$email->getHtmlBody();

        $this->assertEquals(
            $compiledHtml,
            $html
        );

    }

    public function testCompileMessage(): string
    {

        $address = 'Me <me@example.com>';

        self::$mailer->compileMessage(
            __dir__.'/assets/email.php',
            ['user' => 'Test User']
        );

        $html = self::$email->getHtmlBody();

        $this->assertStringContainsString(
            '<!DOCTYPE html>',
            $html
        );

        $this->assertStringContainsString(
            '<html>',
            $html
        );

        $this->assertStringContainsString(
            '<head>',
            $html
        );

        $this->assertStringContainsString(
            '<body>',
            $html
        );

        $this->assertStringContainsString(
            '<h1 style="font-weight: normal;">',
            $html
        );

        $this->assertStringContainsString(
            'Welcome Test User!',
            $html
        );

        $this->assertStringContainsString(
            'cid:assets/logo.email.png',
            $html
        );

        $this->assertEquals(
            'image',
            self::$email->getAttachments()[0]->getMediaType()
        );

        $this->assertSame(
            1,
            count(self::$email->getAttachments())
        );

        return $html;

    }

}