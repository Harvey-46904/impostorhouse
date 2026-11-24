<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Events\SignalReceived;

Route::post('/signals', function (Request $request) {

    $signal = $request->validate([
        'id' => 'required|string',
        'target' => 'required|string',
        'status' => 'required|string',
    ]);

    event(new SignalReceived($signal));

    return response()->json([
        'ok' => true,
        'message' => 'Signal received',
    ]);

});