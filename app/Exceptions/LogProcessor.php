<?php

namespace App\Exceptions;

use Illuminate\Support\Facades\Broadcast;
use Monolog\Handler\HandlerInterface;
use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;

class LogProcessor implements HandlerInterface
{
    public function isHandling(LogRecord $record): bool
    {
        return $record->level->value > Level::Info->value;
    }

    public function handle(LogRecord $record): bool
    {
        if ($record->context['exception']) {
            // $record->extra['exception'] = substr($record->context['exception']->getTraceAsString(), 0, 256);
            $record->extra['exception'] = array_slice($record->context['exception']->getTrace(), 0, 30);
        }
        $record->extra['user'] = auth()->user()?->id ?? "Not specified.";
        $record->extra['department'] = auth()->user()?->department->name ?? "Not specified";
        Broadcast::private('logs')->as('Log')->with($record->toArray())->send();
        return false;
    }

    public function handleBatch(array $records): void
    {
        foreach ($records as $record) {
            $this->handle($record);
        }
    }

    public function close(): void {}
}
