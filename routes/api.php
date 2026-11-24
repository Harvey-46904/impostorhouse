<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Events\SignalReceivedAviator;
use Illuminate\Support\Facades\Http;


Route::post('/signals', function (Request $request) {

    $signal = $request->validate([
        'id' => 'required|string',
        'target' => 'required|string',
        'status' => 'required|string',
    ]);

    // 1. Emitir señal por Reverb
    event(new SignalReceivedAviator($signal));

    // 2. Enviar comando al endpoint manual
    $payload = [
    'commands' => [
        [
            'name' => 'precio aviator 500',
            'type' => 'slaveapp',
            'positionxy' => '160,1270',
            'history' => null,
            'btnmoney' => null,
            'btnplay' => null,
        ],
        [
            'name' => 'precio aviator 5000',
            'type' => 'slaveapp',
            'positionxy' => '336,1270',
            'history' => null,
            'btnmoney' => null,
            'btnplay' => null,
        ],
        [
            'name' => 'play aviator',
            'type' => 'slaveapp',
            'positionxy' => '726,1270',
            'history' => null,
            'btnmoney' => null,
            'btnplay' => null,
        ],
    ],
    'intervalo' => 1000,
    'proveedor' => 'todos',
];

    $response = Http::timeout(10)
        ->post('http://187.77.23.184/api/comando/manual', $payload);

    // 3. Responder
    return response()->json([
        'ok' => true,
        'message' => 'Signal received',
        'command_sent' => $response->successful(),
        'command_response' => $response->json(),
    ]);

});