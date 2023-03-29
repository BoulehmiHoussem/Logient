<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Closure;
use Torann\GeoIP\Facades\GeoIP;

class AccessLogger
{
    /**
     * Logs access to the application.
     *
     * @param Request $request The incoming HTTP request.
     * @return void
     */
    public function logAccess(Request $request): void
    {
        $accessTime = now();
        $accessedLink = $request->fullUrl();
        $userId = auth()->user() ? auth()->user()->id : 'Guest';
        $clientIp = $request->ip();
        $country = GeoIP::getLocation($clientIp)->country;
        
        $userAgent = $request->header('User-Agent');
        $logMessage = "Temps d'accès: {$accessTime} | Lien accédé: {$accessedLink} | Identifiant de l'utilisateur: {$userId} | Adresse IP du client : {$clientIp} | Pays: {$country} | User Agent: {$userAgent}";
        Log::channel('access')->info($logMessage);
    }
}
