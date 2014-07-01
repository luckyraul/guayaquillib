<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>
<?php
// Include soap request class
include('guayaquillib'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'request.php');
include('extender.php');

// Create request object
$request = new GuayaquilRequest($_GET['c'], $_GET['ssd'], Config::$catalog_data);

// Append commands to request
$request->appendGetCatalogInfo();
if (@$_GET['spi'] == 't')
    $request->appendGetWizard();

if (@$_GET['spi2'] == 't')
    $request->appendGetWizard2();

// Execute request
$data = $request->query();

// Check errors
if ($request->error != '') {
    echo $request->error;
} else {
	$cataloginfo = $data[0]->row;
	if (@$cataloginfo['supportvinsearch'] == 'true')
		include('vinsearch.php');

	if (@$cataloginfo['supportframesearch'] == 'true') {
        $formframe = $formframeno = '';
		include('framesearch.php');
    }

	if (@$cataloginfo['supportparameteridentification'] == 'true') {
        $wizard = $data[1];
        include('wizardsearch.php');
    }

	if (@$cataloginfo['supportparameteridentification2'] == 'true') {
        $wizard = $data[1];
		include('wizardsearch2.php');
    }
}
?>
</body>
</html>
