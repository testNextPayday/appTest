<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversations.{code}.{id}', function ($user, $code, $id) {
    switch($code) {
        case \App\Helpers\Constants::ADMIN_CODE:
            return auth('admin')->check() && (auth('admin')->id() == $id);
        case \App\Helpers\Constants::AFFILIATE_CODE:
            return auth('affiliate')->check() && (auth('affiliate')->id() == $id);
        default:
            return false;
    }
});


Broadcast::channel('searchBorrower',function($user){
    return auth()->guard('admin')->check();
});
