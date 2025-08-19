<?php
	try 
	{
		$availableServices = [];
		$folders = glob("../services/*", GLOB_ONLYDIR);
		
		foreach ($folders as $folder)
		{
			array_push($availableServices, basename($folder));
		}
		
		header("Content-Type: application/json");
		echo json_encode($availableServices);
		exit();
	}
	catch (Exception $e)
	{
		http_response_code(404);
	}
?>