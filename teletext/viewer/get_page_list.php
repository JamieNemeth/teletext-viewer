<?php

	// PHP 7.3 fallback
	if (!function_exists('str_starts_with')) {
		function str_starts_with($haystack, $needle) {
			return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
		}
	}
		
	function repeatIndex($originalPageNumbers, $magazine) {
		if (count($originalPageNumbers) > 1 && reset($originalPageNumbers) == $magazine . "00") {
			// repeat P100
			$startIndexForRepeat = ceil(count($originalPageNumbers) / 10);
			$numberOfRepeats = 2;
			$minimumGapBetweenRepeats = 15;
			$lastRepeatIndex = 0;
			$repeatIndex = 0;
			
			for ($i = 0; $i < $numberOfRepeats; $i++)
			{
				if ($lastRepeatIndex + $minimumGapBetweenRepeats < count($originalPageNumbers)) {
					while ($repeatIndex < $lastRepeatIndex + $minimumGapBetweenRepeats)
					{
						$repeatIndex = rand($startIndexForRepeat, count($originalPageNumbers) - 1);
					}
					array_splice($originalPageNumbers, $repeatIndex, 0, $magazine . "00");
				}
				$lastRepeatIndex = $repeatIndex;
			}
		}
		
		return $originalPageNumbers;
	}

	try 
	{
		$service = isset($_GET['service']) ? trim($_GET['service']) : "";
		$recovery = isset($_GET['recovery']) ? trim($_GET['recovery']) : null;
		$magazine = isset($_GET['magazine']) ? trim($_GET['magazine']) : "";
		$repeatIndexPage = !isset($_GET['noRepeatIndex']);
		
		if (($recovery != null || $service != "") && $magazine != "")
		{
			$files = $recovery ? glob("../recoveries/" . $recovery . "/*.{tti,bin}", GLOB_BRACE) : glob("../services/" . $service . "/*.{tti,bin}", GLOB_BRACE);
			if (count($files) == 0) throw new Exception();
			//echo implode("<br>",$files);
			//$regex = "/P([0-9A-F]{3})[^0-9]?.*\.tti/i";
			$regex = "/([1-8]{1}[0-9A-F]{1}[0-9A-F]{1}).*\.(tti|bin)/i";
			
			$pageNumbers = [];
			
			foreach ($files as $file)
			{
				$basenameFile = basename($file);
				preg_match_all($regex, basename($file), $matches);
				
				if ($matches[1])
				{
					$pageNumber = strtoupper($matches[1][0]);
					
					if (str_starts_with($pageNumber, $magazine) && !str_ends_with($pageNumber, "FF"))
					{
						array_push($pageNumbers, $pageNumber);
					}
				}
			}
			
			$pageNumbers = array_unique($pageNumbers);
			//$pageNumbers = array_map('strtoupper', $pageNumbers);
			sort($pageNumbers, SORT_STRING);
			
			if ($repeatIndexPage) $pageNumbers = repeatIndex($pageNumbers, $magazine);
			
			header("Content-Type: application/json");
			echo json_encode($pageNumbers);
			exit();
		}
		else if ($recovery != null || $service != "")
		{
			$files = $recovery ? glob("../recoveries/" . $recovery . "/*.{tti,bin}", GLOB_BRACE) : glob("../services/" . $service . "/*.{tti,bin}", GLOB_BRACE);
			if (count($files) == 0) throw new Exception();
			$regex = "/([1-8]{1}[0-9A-F]{1}[0-9A-F]{1}).*\.(tti|bin)/i";
			
			$allPageNumbersByMagazine = [];
			$allPageNumbersByMagazine[1] = [];
			$allPageNumbersByMagazine[2] = [];
			$allPageNumbersByMagazine[3] = [];
			$allPageNumbersByMagazine[4] = [];
			$allPageNumbersByMagazine[5] = [];
			$allPageNumbersByMagazine[6] = [];
			$allPageNumbersByMagazine[7] = [];
			$allPageNumbersByMagazine[8] = [];
			
			foreach ($files as $file)
			{
				$basenameFile = basename($file);
				preg_match_all($regex, basename($file), $matches);
				
				if ($matches[1])
				{
					$pageNumber = strtoupper($matches[1][0]);
					if (!str_ends_with($pageNumber, "FF")) array_push($allPageNumbersByMagazine[intval(substr($pageNumber, 0, 1))], $pageNumber);
				}
			}				
			
			for ($i = 1; $i <= 8; $i++)
			{
				if (count($allPageNumbersByMagazine[$i]))
				{
					$allPageNumbersByMagazine[$i] = array_unique($allPageNumbersByMagazine[$i]);
					//$allPageNumbersByMagazine[$i] = array_map('strtoupper', $allPageNumbersByMagazine[$i]);
					sort($allPageNumbersByMagazine[$i], SORT_STRING);
					
					if ($repeatIndexPage) $allPageNumbersByMagazine[$i] = repeatIndex($allPageNumbersByMagazine[$i], $i);
				}
			}
			
			header("Content-Type: application/json");
			echo json_encode($allPageNumbersByMagazine);
			exit();
		}
		else
		{
			throw new Exception();
		}
	}
	catch (Exception $e)
	{
		http_response_code(404);
	}
?>