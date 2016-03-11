<?php

$email = "siteops@empowernetwork.com";
$integratorKey = "TEST-74508f95-d55e-47e9-8b05-32916bf5bd10";
$password = "WellinggTonn44";

$url = "https://demo.docusign.net/restapi/v2/login_information?include_account_id_guid=true";
$header = "<DocuSignCredentials><Username>" . $email . "</Username><Password>" . $password . "</Password><IntegratorKey>" . $integratorKey . "</IntegratorKey></DocuSignCredentials>";

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-DocuSign-Authentication: $header"));
$json_response = curl_exec($curl);
$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

if($status==200){
    $response = json_decode($json_response, true);
    print_r(json_encode($response['loginAccounts'][0]));
}else{
    print_r($json_response);
}