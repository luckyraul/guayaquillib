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
include('guayaquillib'.DIRECTORY_SEPARATOR.'render'.DIRECTORY_SEPARATOR.'vehicle'.DIRECTORY_SEPARATOR.'category-floated.php');
include('guayaquillib'.DIRECTORY_SEPARATOR.'render'.DIRECTORY_SEPARATOR.'vehicle'.DIRECTORY_SEPARATOR.'unitlist-floated.php');
include('extender.php');

class CategoryExtender extends CommonExtender
{
	function FormatLink($type, $dataItem, $catalog, $renderer)
	{
	$ssd = (string)$dataItem['ssd'];  // Получаем SSD категории
        if ($type == 'quickgroup')
            $link = 'qgroups.php?c=' . $catalog . '&vid=' . $renderer->vehicleid . '&ssd=' . $renderer->ssd;
        else
            $link = 'vehicle.php?&c=' . $catalog . '&vid=' . $renderer->vehicleid . '&cid=' . $dataItem['categoryid'] . '&ssd=' . ($ssd ? $ssd : $renderer->ssd);

        return $link;
	}	
}

class UnitExtender extends CommonExtender
{
	function FormatLink($type, $dataItem, $catalog, $renderer)
	{
        /*if ($type == 'filter')
            $link = 'unitfilter.php?c=' . $catalog . '&vid=' . $renderer->vehicle_id . '&uid=' . $dataItem['unitid'] .  '&cid=' . $renderer->categoryid . '&ssd=' . $dataItem['ssd'] . '&path_id=' . $renderer->path_id . '&f=' . urlencode($dataItem['filter']);
        else*/
            $link = 'unit.php?c=' . $catalog . '&vid=' . $renderer->vehicle_id . '&uid=' . $dataItem['unitid'] .  '&cid=' . $renderer->categoryid . '&ssd=' . $dataItem['ssd'] . '&path_id=' . $renderer->path_id;

        return $link;
		//return 'unit.php?c='.$catalog.'&uid='.$dataItem['unitid'].'&ssd='.$dataItem['ssd'];
	}	
}

// Create request object
$request = new GuayaquilRequest($_GET['c'], $_GET['ssd'], Config::$catalog_data);

// Append commands to request
$request->appendGetCatalogInfo();
$request->appendGetVehicleInfo($_GET['vid']);
$request->appendListCategories($_GET['vid'], isset($_GET['cid']) ? $_GET['cid'] : -1);
$request->appendListUnits($_GET['vid'], isset($_GET['cid']) ? $_GET['cid'] : -1);

// Execute request
$data = $request->query();

// Check errors
if ($request->error != '')
{
    echo $request->error;
}
else
{       $catalogInfo = $data[0]->row;
		$vehicle = $data[1]->row;
		$categories = $data[2];
		$units = $data[3];

		echo '<h1>'.CommonExtender::FormatLocalizedString('CarName', $vehicle['name']).'</h1>';

		echo '<div id="pagecontent">';

		$renderer = new GuayaquilCategoriesList(new CategoryExtender());
		echo $renderer->Draw($_GET['c'], $categories, $_GET['vid'], $_GET['cid'], $_GET['ssd'],$catalogInfo);
			  
		$renderer = new GuayaquilUnitsList(new UnitExtender());
		$renderer->imagesize = 250;
		echo $renderer->Draw($_GET['c'], $units);

	  echo '</div>';
}
?>
</body>
</html>
