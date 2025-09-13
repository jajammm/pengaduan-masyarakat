<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class GeocodeProxyController extends Controller
{
    public function reverse(Request $request)
    {
        $lat = $request->query('lat');
        $lon = $request->query('lon');
        if (!$lat || !$lon) {
            return response()->json(['error' => 'lat/lon required'], 400);
        }
        $url = 'https://nominatim.openstreetmap.org/reverse?format=json&lat=' . $lat . '&lon=' . $lon . '&zoom=18&addressdetails=1';
        $response = Http::withHeaders([
            'User-Agent' => 'pengaduan-masyarakat/1.0'
        ])->get($url);
        return $response->json();
    }
}
