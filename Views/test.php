<?php

use Request\HttpClient;

// $client = new HttpClient('https://api2.eu.prismacloud.io');

// $data = [
//     'username' => '7e9f2547-eca6-4249-85c3-6070511f8882',
//     'password' => 'KLMrUHB+pSLWWV84oPNy91b2vrw=',
//     'prismaId' => '807098434757649408'
// ];

// $request = $client->call('POST', '/login', $data, null, true);

// echo $request['token'];

$baseUrl = 'https://azure-waf-manager-api.sunwellsolutions.com';

$token = 'eyJhbGciOiJIUzI1NiJ9.eyJhY2Nlc3NLZXlJZCI6IjdlOWYyNTQ3LWVjYTYtNDI0OS04NWMzLTYwNzA1MTFmODg4MiIsInN1YiI6ImRpbWl0YXIuZGpvbmdvdkB1ZWZhLmNoIiwiZmlyc3RMb2dpbiI6ZmFsc2UsInByaXNtYUlkIjoiODA3MDk4NDM0NzU3NjQ5NDA4IiwiaXBBZGRyZXNzIjoiOTIuMjQ3LjU3LjE3OSIsImlzcyI6Imh0dHBzOi8vYXBpMi5ldS5wcmlzbWFjbG91ZC5pbyIsInJlc3RyaWN0IjowLCJpc0FjY2Vzc0tleUxvZ2luIjp0cnVlLCJ1c2VyUm9sZVR5cGVEZXRhaWxzIjp7Imhhc09ubHlSZWFkQWNjZXNzIjp0cnVlfSwidXNlclJvbGVUeXBlTmFtZSI6IkFjY291bnQgR3JvdXAgUmVhZCBPbmx5IiwiaXNTU09TZXNzaW9uIjpmYWxzZSwibGFzdExvZ2luVGltZSI6MTcwNjA5NzcyODMyMSwiYXVkIjoiaHR0cHM6Ly9hcGkyLmV1LnByaXNtYWNsb3VkLmlvIiwidXNlclJvbGVUeXBlSWQiOjMsImF1dGgtbWV0aG9kIjoiUEFTU1dPUkQiLCJzZWxlY3RlZEN1c3RvbWVyTmFtZSI6IlVuaW9uIG9mIEV1cm9wZWFuIEZvb3RiYWxsIEFzc29jaWF0aW9ucyAoVUVGQSkgLSA0NTQyNjI3NTgwMjU5MTQ0MDM4Iiwic2Vzc2lvblRpbWVvdXQiOjMwLCJ1c2VyUm9sZUlkIjoiZDE1OGM3YjEtNGEwOC00NDJlLWE4ZWItNjZiYWM5NmI4ODgwIiwiaGFzRGVmZW5kZXJQZXJtaXNzaW9ucyI6dHJ1ZSwiZXhwIjoxNzA2MTAwOTM3LCJpYXQiOjE3MDYxMDAzMzcsInVzZXJuYW1lIjoiZGltaXRhci5kam9uZ292QHVlZmEuY2giLCJ1c2VyUm9sZU5hbWUiOiJQcmlzbWFfRGV2U2VjT1BTX1RlYW0ifQ.3GTVvg8VBp8eX4hDxLIqSA8zrRojQ9IrLho8hHCLp3s';

$request = new HttpClient($baseUrl);

$headers = [
    'x-api-key' => '57a7c24eb9a17c9b8e0d149586143c0e9c518083185375b8ff994a67b99c4df0',
    'Accept' => 'application/json'
];

$data = [
    'assetId' => 'i-085846ef947d215c8',
    'type' => 'external_finding',
    'findingType' => 'HOST_VULNERABILITY_CVE',
    'riskFactors' => 'CRITICAL_SEVERITY'
];



$response = $request->call('GET', '/v1/organization', null, null, false, $headers);
echo '<pre>';
print_r($response);
echo '</pre>';
