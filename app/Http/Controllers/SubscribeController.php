<?php

namespace App\Http\Controllers;

use App\Models\Subscribe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscribeController extends Controller
{

    /**
     * @return mixed
     */
    public function getSubscribes()
    {
        return Subscribe::where('active', true)->get();
    }

    /**
     * @return mixed
     */
    public function getUserSubscribe()
    {
        $user = Auth::user()->load('subscribe', 'publications');
        return response()->json($user->subscribe);
    }

}
