<?php

namespace PowerDI\Emails;

use PHPMailer\PHPMailer\PHPMailer;
use PowerDI\Core\Autowired;
use PowerDI\Loaders\PathResolver;
use PowerDI\Templates\AbstractTemplater;

class PHPMailerEmailService implements EmailService {
    private PHPMailer $mail;
    #[Autowired("@AbstractTemplater")]
    private AbstractTemplater $templater;
    #[Autowired("@PathResolver")]
    private PathResolver $pathResolver;

    public function __construct(array $properties) {
        $this->mail = new PHPMailer(true);
        if (isset($properties["smtp"])) {
            $this->mail->isSMTP();
        }

        if (isset($properties["host"])) {
            $this->mail->Host = $properties["host"];                     //Set the SMTP server to send through
        }

        if (isset($properties["auth"])) {
            $this->mail->SMTPAuth = $properties["auth"];                     //Set the SMTP server to send through
        }

        if (isset($properties["username"])) {
            $this->mail->Username = $properties["username"];                     //Set the SMTP server to send through
        }

        if (isset($properties["password"])) {
            $this->mail->Password = $properties["password"];                     //Set the SMTP server to send through
        }

        if (isset($properties["secure"])) {
            $this->mail->SMTPSecure = $properties["secure"];                     //Set the SMTP server to send through
        }

        if (isset($properties["port"])) {
            $this->mail->Port = $properties["port"];                     //Set the SMTP server to send through
        }
    }

    private function clear() {
        $this->mail->clearAddresses();
        $this->mail->clearBCCs();
        $this->mail->clearCCs();
        $this->mail->clearAttachments();
        $this->mail->clearAllRecipients();
        $this->mail->clearCustomHeaders();
        $this->mail->clearReplyTos();
    }
    public function send(Email $email) {
        $this->mail->setFrom($email->getFrom()->getAddress(), $email->getFrom()->getAlias());
        foreach ($email->getRecipients() as $recipient) {
            $this->mail->addAddress($recipient->getAddress(), $recipient->getAlias());
        }

        $this->mail->isHTML($email->isHtml());

        $this->mail->Subject = $email->getSubject();

        if($email->getBody() instanceof TemplatedBody) {
            $this->mail->Body = $this->templater->renderToString($this->pathResolver->resolveTemplate($email->getBody()->getTemplate()), $email->getBody()->getArguments());
        } else {
            $this->mail->Body = $email->getBody();
        }

        $this->mail->send();
        $this->clear();
    }
}