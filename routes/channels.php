<?php

use App\Models\Message;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('test',function($user,$id){
    return false;
});

Broadcast::channel('chat.gloable' , function(Message $message){
    return true;
});