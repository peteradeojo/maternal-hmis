<?php

namespace App\Exceptions;

use Illuminate\Support\Facades\Broadcast;
use Monolog\Handler\HandlerInterface;
use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;

class LogProcessor implements HandlerInterface {
    public function isHandling(LogRecord $record): bool
    {
        return $record->level->value > Level::Info->value;
    }

    public function handle(LogRecord $record): bool
    {
        Broadcast::private('logs')->as('Log')->with($record->toArray())->send();
        return false;
    }

    public function handleBatch(array $records): void
    {
        foreach($records as $record) {
            $this->handle($record);
        }
    }

    public function close(): void
    {

    }
}
