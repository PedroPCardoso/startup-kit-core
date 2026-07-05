<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Contracts;

use Cardoso\StartupKit\Core\Primitives\Result\Result;

interface Notifier
{
    public function send(string $channel, string $recipient, string $subject, string $body): Result;

    public function sendRaw(string $channel, array $payload): Result;
}
