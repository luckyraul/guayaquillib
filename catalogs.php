<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<!---->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>
<?php

// Include soap request class
include('guayaquillib'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'request.php');
// Include catalog list view
include('guayaquillib'.DIRECTORY_SEPARATOR.'render'.DIRECTORY_SEPARATOR.'catalogs'.DIRECTORY_SEPARATOR.'2coltable.php');

include('extender.php');

class CatalogExtender extends CommonExtender
{
    function FormatLink($type, $dataItem, $catalog, $renderer)
    {
        $link = 'catalog.php?&c='.$dataItem['code'].'&ssd='.$dataItem['ssd'];
        if ($dataItem['supportparameteridentification'] == 'true')
            $link .= '&spi=t';

        if ($dataItem['supportparameteridentification2'] == 'true')
            $link .= '&spi2=t';

        return $link;
    }   
}

// Create request object
$request = new GuayaquilRequest('', '', Config::$catalog_data);

// Append commands to request
$request->appendListCatalogs();

// Execute request
$data = $request->query();

// Check errors
if ($request->error != '')
{
    echo $request->error;
}
else
{
    // Create GuayaquilCatalogsList object. This class implements default catalogs list view
    $renderer = new GuayaquilCatalogsList(new CatalogExtender());

    // Configure columns
    $renderer->columns = array('icon', 'name', 'version');

    // Draw catalogs list
    echo $renderer->Draw($data[0]);
}
?>
</body>
</html>