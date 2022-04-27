<?php

namespace ITColima\SiitecApi\Model\App;

use Francerz\Http\Utils\HttpHelper;
use JsonSerializable;
use Psr\Http\Message\MessageInterface;

class Email implements JsonSerializable
{
    private $to = [];
    private $cc = [];
    private $bcc = [];
    private $replyTo = [];
    private $contentType = 'text/plain';
    private $charset = null;
    private $subject = '';
    private $body = '';

    public static function fromHttpMessage(MessageInterface $message)
    {
        $content = HttpHelper::getContent($message);
        $email = new static();
        $email->to = is_object($content->to) || is_array($content->to) ? (array)$content->to : [];
        $email->cc = is_object($content->cc) || is_array($content->cc) ? (array)$content->cc : [];
        $email->bcc = is_object($content->bcc) || is_array($content->bcc) ? (array)$content->bcc : [];
        $email->replyTo = is_object($content->replyTo) || is_array($content->replyTo) ? (array)$content->replyTo : [];
        $email->contentType = is_string($content->contentType) ? $content->contentType : 'text/plain';
        $email->charset = is_string($content->charset) ? $content->charset : null;
        $email->subject = is_string($content->subject) ? $content->subject : '';
        $email->body = is_string($content->body) ? $content->body : '';
        return $email;
    }

    public function jsonSerialize()
    {
        return array(
            'to' => $this->to,
            'cc' => $this->cc,
            'bcc' => $this->bcc,
            'replyTo' => $this->replyTo,
            'contentType' => $this->contentType,
            'charset' => $this->charset,
            'subject' => $this->subject,
            'body' => $this->body
        );
    }

    public function addTo(string $address, ?string $name = null)
    {
        if (isset($name)) {
            $this->to[$address] = $name;
        } else {
            $this->to[] = $address;
        }
    }

    public function getTo()
    {
        return $this->to;
    }

    public function addCc(string $address, ?string $name = null)
    {
        if (isset($name)) {
            $this->cc[$address] = $name;
        } else {
            $this->cc[] = $address;
        }
    }

    public function getCc()
    {
        return $this->cc;
    }

    public function addBcc(string $address, ?string $name = null)
    {
        if (isset($name)) {
            $this->bcc[$address] = $name;
        } else {
            $this->bcc[] = $address;
        }
    }

    public function getBcc()
    {
        return $this->bcc;
    }

    public function addReplyTo(string $address, ?string $name = null)
    {
        if (isset($name)) {
            $this->replyTo[$address] = $name;
        } else {
            $this->replyTo[] = $address;
        }
    }

    public function getReplyTo()
    {
        return $this->replyTo;
    }

    public function setContentType(string $contentType)
    {
        $this->contentType = $contentType;
    }

    public function getContentType()
    {
        return $this->contentType;
    }

    public function setSubject(string $subject)
    {
        $this->subject = $subject;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setBody(string $body)
    {
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }
}
