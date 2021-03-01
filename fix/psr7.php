<?php
namespace GuzzleHttp\Psr7;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;


if(!function_exists("get_message_body_summary")) {
    /**
     * Get a short summary of the message body
     *
     * Will return `null` if the response is not printable.
     *
     * @param MessageInterface $message The message to get the body summary
     * @param int $truncateAt The maximum allowed size of the summary
     *
     * @return null|string
     */
    function get_message_body_summary(MessageInterface $message, $truncateAt = 120)
    {
        $body = $message->getBody();

        if (!$body->isSeekable() || !$body->isReadable()) {
            return null;
        }

        $size = $body->getSize();

        if ($size === 0) {
            return null;
        }

        $summary = $body->read($truncateAt);
        $body->rewind();

        if ($size > $truncateAt) {
            $summary .= ' (truncated...)';
        }

        // Matches any printable character, including unicode characters:
        // letters, marks, numbers, punctuation, spacing, and separators.
        if (preg_match('/[^\pL\pM\pN\pP\pS\pZ\n\r\t]/', $summary)) {
            return null;
        }

        return $summary;
    }
}