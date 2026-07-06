<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Contracts;

use PedroPCardoso\StartupKit\Core\Primitives\Result\Result;

interface Notifier
{
    public function send(string $channel, string $recipient, string $subject, string $body): Result;

    public function sendRaw(string $channel, array $payload): Result;
}
