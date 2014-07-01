<?php

require_once 'SSLSoapClient.php';

class GuayaquilSoapWrapper
{
    ///////////////////////////// Configuration

    var $certificatePath = 'cert';

    var $certificateFileName = 'client.pem';

    var $keyFileName = 'client.key';

    var $certificatePassword = "123ertGHJ";

    //////////////////////////// Results

    // If error != '' then request processing error occured
    var $error = '';

    // SimpleXML object
    var $data;

    function __construct()
    {
        $this->certificatePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cert';
    }

    function getSoapClient($service_type = 'oem', $useCertificate = true)
    {
        $options = array(
            'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
        );

        if ($service_type == 'oem') {
            $options['uri'] = 'http://WebCatalog.Kito.ec';
            $options['location'] = 'https://10.1.1.246/ec.Kito.WebCatalog/services/Catalog.CatalogHttpSoap11Endpoint/';
        } else if ($service_type == 'am') {
            $options['uri'] = 'http://Aftermarket.Kito.ec';
            $options['location'] = 'https://aws.laximo.net/ec.Kito.Aftermarket/services/Catalog.CatalogHttpSoap11Endpoint/';
        } else if ($service_type == 'prag') {
            $options['uri'] = 'http://Prag.Kito.ec';
            $options['location'] = 'https://10.1.1.246/ec.Kito.Prag/services/PriceAggregator.PriceAggregatorHttpSoap11Endpoint/';
        } else {
            throw new Exception("Unknown service type {$service_type}");
        }

        if ($useCertificate)
        {
            $options['sslCertPath'] = $this->certificatePath . DIRECTORY_SEPARATOR . $this->certificateFileName;
            $options['sslKeyPath'] = $this->certificatePath . DIRECTORY_SEPARATOR . $this->keyFileName;
        }

        $client = new SSLSoapClient(null, $options);

        return $client;
    }

    function queryData($request, $service_type = 'oem', $login = false, $secretKey = false)
    {
        try {
            $client = $this->getSoapClient($service_type, !$login || !$secretKey);
            if ($login && $secretKey)
                $this->data = $client->QueryDataLogin($request, $login, md5($request.$secretKey));
            else
                $this->data = $client->QueryData($request);

            return $this->data;
        } catch (Exception $ex) {
            $this->error = $this->parseError($ex->getMessage());
        }
    }

    function parseError($err)
    {
        if (strpos($err, "cURL ERROR: 35"))
            return 'Not Connected';

        if (strpos($err, "cURL ERROR: 58"))
            return 'No Certificate';

        if (strpos($err, "400 Bad Request"))
            return 'Certificate expired';

        $e = explode("<br>", $err, 2);
        $err = $e[0];
        $pos = strrpos($err, 'E_');
        if ($pos === false)
            $pos = strrpos($err, ':') + 1;

        return substr($err, $pos);
    }

    function queryDataCheckException($request)
    {
        try {
            $this->data = $this->queryData($request);
            return $this->data;
        } catch (SoapFault $exception) {
            $this->error = $exception->faultstring;
        }
    }
}

?>
