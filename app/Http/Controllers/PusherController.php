<?php

namespace App\Http\Controllers;

use App\Events\PusherBroadcast;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PusherController extends Controller
{
    public function index(): View|Factory
    {
        return view('index');
    }

    public function broadcast(Request $request): View|Application|Factory|JsonResponse|\Illuminate\Contracts\Foundation\Application
    {
        $message = $request->get('message');
        if ($message === null) {
            return response()->json(['error' => 'Message is required'], 400);
        }

        broadcast(new PusherBroadcast($message))->toOthers();

        return view('broadcast', ['message' => $message]);
    }

    public function receive(Request $request): View|Factory
    {
        return view('receive', ['message' => $request->input('message')]);
    }
}
