<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('test',function($user,$id){
    return false;
});