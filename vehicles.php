<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>
<?php
// Include soap request class
include('guayaquillib'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'request.php');
// Include view class
include('guayaquillib'.DIRECTORY_SEPARATOR.'render'.DIRECTORY_SEPARATOR.'vehicles'.DIRECTORY_SEPARATOR.'vehicletable.php');

include('extender.php');

class VehiclesExtender extends CommonExtender
{
    function FormatLink($type, $dataItem, $catalog, $renderer)
    {
        if (!$catalog)
            $catalog = $dataItem['catalog'];
        $link = ($renderer->qg ? 'qgroups' : 'vehicle').'.php?c=' . $catalog . '&vid=' . $dataItem['vehicleid'] . '&ssd=' . $dataItem['ssd'] . '&path_data=' . urlencode(base64_encode(substr($dataItem['vehicle_info'], 0, 300)));

        return $link;
		//return 'vehicle.php?c='.$catalog.'&vid='.$dataItem['vehicleid'].'&ssd='.$dataItem['ssd'];
    }   
}

// Create request object
$request = new GuayaquilRequest($_GET['c'], $_GET['ssd'], Config::$catalog_data);

// Append commands to request
if ($_GET['ft'] == 'findByVIN')
	$request->appendFindVehicleByVIN($_GET['vin']);
else if ($_GET['ft'] == 'findByFrame')
	$request->appendFindVehicleByFrame($_GET['frame'], $_GET['frameNo']);
else if ($_GET['ft'] == 'findByWizard')
    $request->appendFindVehicleByWizard($_GET['wid']);
else if ($_GET['ft'] == 'findByWizard2')
    $request->appendFindVehicleByWizard2($_GET['ssd']);

$request->appendGetCatalogInfo();

// Execute request
$data = $request->query();

// Check errors
if ($request->error != '')
{
    echo $request->error;
}
else
{
	$vehicles = $data[0];
	$cataloginfo = $data[1]->row;
	
	if (is_object($vehicles) == false || $vehicles->row->getName() == '') 
	{
		if ($_GET['ft'] == 'findByVIN')
			echo CommonExtender::FormatLocalizedString('FINDFAILED', $_GET['vin']);
		else
			echo CommonExtender::FormatLocalizedString('FINDFAILED', $_GET['frame'].'-'.$_GET['frameNo']);
	} else
	{
		echo '<h1>'.CommonExtender::LocalizeString('Cars').'</h1><br>';

	  // Create data renderer
	  $renderer = new GuayaquilVehiclesList(new VehiclesExtender());
      $renderer->columns = array('name', 'date', 'datefrom', 'dateto', 'model', 'framecolor', 'trimcolor', 'modification', 'grade', 'frame', 'engine', 'engineno', 'transmission', 'doors', 'manufactured', 'options', 'creationregion', 'destinationregion', 'description', 'remarks');

      $renderer->qg = (string)$cataloginfo['supportquickgroups'] == 'true';

	  // Draw data
	  echo $renderer->Draw($_GET['c'], $vehicles);
	}

	if ($cataloginfo['supportvinsearch'] == 'true') 
	{
		$formvin = $_GET['vin'];
		include('vinsearch.php');
	}

	if ($cataloginfo['supportframesearch'] == 'true')
	{
		$formframe = $_GET['frame'];
		$formframeno = $_GET['frameNo'];
		include('framesearch.php');
	}
}

?>
</body>
</html>
