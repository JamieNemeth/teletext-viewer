<?php
	try 
	{
		$availableRecoveries = [];
		$folders = glob("../recoveries/*", GLOB_ONLYDIR);
		
		foreach ($folders as $folder)
		{
			array_push($availableRecoveries, basename($folder));
		}

		if ($echo)
		{
			header("Content-Type: application/json");
			echo json_encode($availableRecoveries);
			exit();
		}
	}
	catch (Exception $e)
	{
		http_response_code(404);
	}
?>