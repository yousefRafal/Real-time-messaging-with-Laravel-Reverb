<?php

namespace App\Console\Commands;

use App\Events\ChatMessage;
use App\Models\Message;
use Illuminate\Console\Command;
use function Laravel\Prompts\text;

class SendMessageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send message to chat';

    /**
     * Execute the console command.
     */
    public function handle()
    {

            $conntent = text(label: 'my name is ',
            required:true
        );
        $channel = text(
            'general',
            required: true
        );
            $message = Message::createMessage(
                $conntent,
                $channel
            );
        ChatMessage::dispatch($message);
    }
}
