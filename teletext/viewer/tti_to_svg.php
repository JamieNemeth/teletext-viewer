<?php

	// PHP 7.3 fallback
	if (!function_exists('str_starts_with')) {
		function str_starts_with($haystack, $needle) {
			return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
		}
	}

	header('Content-type: image/svg+xml; charset=utf-8'); //comment out to debug (will return as PHP content instead of SVG)
	echo '<?xml version="1.0" standalone="no"?>
		<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" 
		"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">';

	if (!defined("SVG_SPACE_CHAR")) define("SVG_SPACE_CHAR", "&#160;");
	if (!defined("SVG_BACKGROUND_CHAR")) define("SVG_BACKGROUND_CHAR", "");
	if (!defined("SVG_X_OFFSET")) define("SVG_X_OFFSET", 100);
	if (!defined("SVG_Y_OFFSET")) define("SVG_Y_OFFSET", 1);
	if (!defined("SVG_BACKGROUND_WIDTH")) define("SVG_BACKGROUND_WIDTH", 3152);
	if (!defined("SVG_BACKGROUND_HEIGHT")) define("SVG_BACKGROUND_HEIGHT", 2560);
	if (!defined("SVG_LINE_HEIGHT")) define("SVG_LINE_HEIGHT", 96);
	if (!defined("SVG_FONT_SIZE")) define("SVG_FONT_SIZE", 100);
	if (!defined("SVG_LETTER_SPACING")) define("SVG_LETTER_SPACING", -3);
	
	if (!defined("TELETEXT_PAGE_FILE_REGEX")) define("TELETEXT_PAGE_FILE_REGEX", "/([1-8]{1}[0-9A-F]{1}[0-9A-F]{1}).*\.(tti|bin)/i");
	
	if (!function_exists('getPageList'))
	{
		function getPageList($service, $recovery, $magazine) {
			if ($service != null || $recovery != null)
			{
				if ($recovery)
				{
					$files = glob("../recoveries/" . $recovery . "/*.{tti,bin}", GLOB_BRACE);
				}
				else
				{
					$files = glob("../services/" . $service . "/*.{tti,bin}", GLOB_BRACE);
				}
				if (count($files) == 0) throw new Exception();
				//$regex = "/([1-8]{1}[0-9A-F]{1}[0-9A-F]{1}).*\.(tti|bin)/i";
				
				$pageNumbers = [];
				
				foreach ($files as $file)
				{
					$basenameFile = basename($file);
					//preg_match_all($regex, basename($file), $matches);
					preg_match_all(TELETEXT_PAGE_FILE_REGEX, basename($file), $matches);
					
					if ($matches[1])
					{
						$pageNumber = $matches[1][0];
						
						if (str_starts_with($pageNumber, $magazine) && !str_ends_with($pageNumber, "FF"))
						{
							array_push($pageNumbers, $pageNumber);
						}
					}
				}
				
				$pageNumbers = array_unique($pageNumbers);
				//uncomment if using for more than just an array count
				//$pageNumbers = array_map('strtoupper', $pageNumbers);
				//sort($pageNumbers, SORT_STRING);
				return $pageNumbers;
			}
			else 
			{
				return [];
			}
		}
	}

	$requestedService = $_GET['service'] ?? null;
	$requestedRecovery = $_GET['recovery'] ?? null;
	$requestedPage = strtoupper($_GET['page']) ?? "100";
	$requestedMagazine = substr($requestedPage, 0, 1);
	$requestedSubpage = $_GET['subpage'] ?? null;
	$pageSearchSpeed = $_GET['pageSearchSpeed'] ?? 180;
	
	$headerOnly = isset($_GET['headeronly']) ? true : false;
	
	$resize = isset($_GET['resize']) ? $_GET['resize'] : "0";
	
	$randomId = "svg" . random_int(0, PHP_INT_MAX);
	
	$outputSubpages = [];
	
	$suppressNextLineFlag = false; //for double height text
	
	$pageList = getPageList($requestedService, $requestedRecovery, $requestedMagazine);
	
	if (!class_exists('Subpage'))
	{
		class Subpage 
		{
			public $cycleTime;
			public $outputLines = array();
			public $fastextLinks = array();
			
			public function __construct()
			{
				$this->cycleTime = 12;
				$this->outputLines = array_fill(0, 25, null);
			}
			
			function getCycleTime() 
			{
				return $this->cycleTime;
			}
			
			function setCycleTime($cycleTime) 
			{
				$this->cycleTime = $cycleTime;
			}
			
			function getOutputLines()
			{
				return $this->outputLines;
			}
			
			function setOutputLine($outputLineIndex, $outputLine)
			{
				$this->outputLines[$outputLineIndex] = $outputLine;
			}
			
			function getFastextLink($fastextColour)
			{
				return array_key_exists($fastextColour, $this->fastextLinks) ? $this->fastextLinks[$fastextColour] : null;
			}
			
			function setFastextLink($fastextColour, $page)
			{
				$this->fastextLinks[$fastextColour] = $page;
			}
		}
	}
	
	if (!function_exists('convertCharacters')) 
	{
		function convertCharacters($inputContents)
		{
			$search = array(
				"", //x82
				"", //x96
				"", //x99
				"", //x91
				"", //x9D
				"", //x94
				"", //x98
				"", //x9E
				"", //x9F
				"", //x93
				"", //x92
				"", //x97
				
				//from TRE
				"", 
				"",
				
				"",
				"",
				"",
				"",
				"",
				"",
				"",
				"",
				"",
				"",
				"",
				"",
				"",
				
				"",
				"",
				"",
				"",
				"",
				"",
				"",
				"",
				"",
				"",
				"",
				
				"",
				
				"",
				
				"",
				"",
				"",
				"",
				
				"\r", //redundant end of line character
			);
				
			$replace = array(
				"B", //x82
				"V", //x96
				"Y", //x99
				"Q", //x91
				"]", //x9D
				"T", //x94
				"X", //x98
				"^", //x9E
				"_", //x9F
				"S", //x93
				"R", //x92
				"W", //x97
				
				//from TRE
				"A",
				"@",
				
				"W",
				"^",
				"O",
				"S",
				"Z",
				"V",
				"^",
				"_",
				"X",
				"D",
				"M",
				"]",
				"C",
				
				"R",
				"\\",
				"L",
				"^",
				"U",
				"N",
				"Q",
				"O",
				"T",
				"O",
				"G",
				
				"E",
				
				"F",
				
				"H",
				"K",
				"I",
				"J",
				
				"", //redundant end of line character
			);
			
			$outputContents = str_replace($search, $replace, $inputContents);
			
			return $outputContents;
		}
	}
	
	//get vbit header rows if they exist
	$vbitHeaderRow = null;	
	
	if (!is_null($requestedRecovery) || !is_null($requestedService))
	{
		$vbitConfFilename = $requestedRecovery ? "../restorations/tti-teletext-restorations/" . $requestedRecovery . "/vbit.conf" : "../services/" . $requestedService . "/vbit.conf";
		if (!file_exists($vbitConfFilename)) $vbitConfFilename = "../recoveries/" . $requestedRecovery . "/vbit.conf";
		
		if (file_exists($vbitConfFilename))
		{
			//custom templates unlikely to exist if vbit.conf doesn't exist
			$vbitCustomTemplateFilename = $requestedRecovery 
				? "../restorations/tti-teletext-restorations/" . $requestedRecovery . "/P" . $requestedMagazine . "FF.tti" 
				: "../services/" . $requestedService . "/P" . $requestedMagazine . "FF.tti";
			
			if (file_exists($vbitCustomTemplateFilename))
			{
				$vbitCustomTemplateFile = fopen($vbitCustomTemplateFilename, "r");
				
				if ($vbitCustomTemplateFile)
				{
					$lines = explode("\n", fread($vbitCustomTemplateFile, filesize($vbitCustomTemplateFilename)));
					foreach ($lines as $line)
					{
						if (str_starts_with($line, 'OL,0,')) 
						{
							$vbitHeaderRow = substr($line, strlen('OL,0,'));
							break;
						};
					}
				
					fclose($vbitCustomTemplateFile);
				}
			}
			else
			{
				$vbitConfFile = fopen($vbitConfFilename, "r");
				
				if ($vbitConfFile)
				{
					$lines = explode("\n", fread($vbitConfFile, filesize($vbitConfFilename)));
					foreach ($lines as $line)
					{
						if (str_starts_with($line, 'header_template=')) 
						{
							$vbitHeaderRow = "        " . substr($line, strlen('header_template='));
							break;
						};
					}
					
					fclose($vbitConfFile);
				}
			}
		}
	}
	
	
	try 
	{
		$subpages = [];
		
		if (isset($_POST["tti"])) {
			$subpages = preg_split('@(?=PN,)@', $_POST["tti"]);
			$header = array_shift($subpages);
		}
		else if ((!is_null($requestedService) || !is_null($requestedRecovery)) && ctype_xdigit($requestedPage))
		{
			$ttiFiles = $requestedRecovery ? glob("../recoveries/" . $requestedRecovery . "/*.tti") : glob("../services/" . $requestedService . "/*.tti");
			$matchingTtiFile = null;
			
			foreach ($ttiFiles as $file)
			{
				$basenameFile = basename($file);
				preg_match_all("/(" . $requestedPage . ").*\.tti/i", basename($file), $matches);
				
				if ($matches[1])
				{
					$matchingTtiFile = $file;
					break;
				}
			}
			
			//$ttiFiles = glob("../services/" . $requestedService . "/*" . sprintf('%03X', hexdec($requestedPage)) . "*.tti");
			//if (count($ttiFiles) != 0) 
			if ($matchingTtiFile)
			{
				//$ttiFile = fopen($ttiFiles[0], "r");
				$ttiFile = fopen($matchingTtiFile, "r");
			
				if ($ttiFile)
				{
					$ttiFileContents = utf8_encode(fread($ttiFile, filesize($matchingTtiFile)));
					$ttiFileContentsWithCharactersConverted = convertCharacters($ttiFileContents);
					
					//var_dump($ttiFileContents);
					
					$subpages = preg_split('@(?=PN,)@', $ttiFileContentsWithCharactersConverted);
					$header = array_shift($subpages);
					
					fclose($ttiFile);
				}
			}
			else 
			{				
				$binFiles = $requestedRecovery ? glob("../recoveries/" . $requestedRecovery . "/*.bin") : glob("../services/" . $requestedService . "/*.bin");
				$matchingBinFile = null;
				
				foreach ($binFiles as $file)
				{
					$basenameFile = basename($file);
					preg_match_all("/(" . $requestedPage . ").*\.bin/i", basename($file), $matches);
					if ($matches[1])
					{
						$matchingBinFile = $file;
						break;
					}
				}
				
				//if (count($binFiles) != 0)
				if ($matchingBinFile)
				{
					$binToTtiEcho = false;
					include("bin_to_tti.php");
					$binFile = $binToTtiOutputString;
					
					if ($binFile)
					{
						$subpages = preg_split('@(?=PN,)@', $binFile);
						$header = array_shift($subpages);
					}
					else
					{
						throw new Exception();
					}
				}
				else
				{
					throw new Exception();
				}
			}
		}
		else
		{
			throw new Exception();
		}
		
		if (!empty($subpages))
		{
			$subpagesCount = count($subpages);
			for ($s = 0; $s < $subpagesCount; $s++) 
			{
				$subpage = new Subpage();
				
				//populate outputSubpages
				$lines = explode("\n", $subpages[$s]);
				foreach ($lines as $line)
				{
					if (isset($_POST["tti"]) && str_starts_with($line, 'PN,')) 
					{
						$pageNumberLineSegments = explode(',', $line);
						$requestedPage = strtoupper(substr($pageNumberLineSegments[1], 0, 3));
					};
					
					if (str_starts_with($line, 'OL,')) 
					{
						$outputLineSegments = preg_split("/^OL,(\d*),/", $line, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
						if (count($outputLineSegments) >= 2)
						{
							if (intval($outputLineSegments[0]) <= 24) $subpage->setOutputLine($outputLineSegments[0], $outputLineSegments[1]);
						}
					};
					
					if (str_starts_with($line, 'CT,')) 
					{
						$outputLineSegments = preg_split("/^CT,(\d*),/", $line, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
						if ($outputLineSegments[1] == "T") {
							$subpage->setCycleTime($outputLineSegments[0]);
						}
						else if ($outputLineSegments[1] == "C") {
							$subpage->setCycleTime(($pageSearchSpeed * count($pageList)) / 1000);
						}
					};
					
					if (str_starts_with($line, 'FL,')) 
					{
						$fastextLineSegments = explode(',', $line);
						$subpage->setFastextLink("red", strtoupper($fastextLineSegments[1]));
						$subpage->setFastextLink("green", strtoupper($fastextLineSegments[2]));
						$subpage->setFastextLink("yellow", strtoupper($fastextLineSegments[3]));
						$subpage->setFastextLink("blue", strtoupper($fastextLineSegments[4]));
						
						$subpage->setFastextLink("index", strtoupper(substr($fastextLineSegments[6], 0, 3)));
					};
				}
				
				if (!is_null($vbitHeaderRow))
				{
					$subpage->setOutputLine(0, $vbitHeaderRow);
				}
				
				array_push($outputSubpages, $subpage);
			}
		}
		else
		{
			throw new Exception();
		}
	}
	catch (Exception $e)
	{
		http_response_code(404);
		echo "<svg><text>".$e."</text></svg>";
	}
	
	if (!function_exists('ttiOutputLineToSvg')) 
	{
		function ttiOutputLineToSvg($ttiOutputLineIndex, $ttiOutputLine)
		{
			global $suppressNextLineFlag;
			
			if ($suppressNextLineFlag) 
			{
				$suppressNextLineFlag = false;
				return null;
			}
			
			$svgOutputLine = str_pad($ttiOutputLine, 60);
			
			//need to go character by character
			$svgBackgroundOutput = "";
			$svgForegroundOutput = "";
			
			$lastMosaicCharacter = null;
			$lastNonControlCharacter = null;
			$lastControlCharacter = null;
			$holdMosaicCharacter = null;
			$backgroundCharacter = null;
			
			$mostRecentColour = null;
			
			//separate and draw background
			//separate and draw foreground text
			//separate and draw foreground graphics
			
			$graphicsModeFlag = false;
			$separatedGraphicsModeFlag = false;
			$holdGraphicsFlag = false;
			
			$doubleHeightFlag = false;
			
			$printedCharacterCount = 0;
			$unclosedTspansCount = 0;
			$lastUnclosedTspanType = "";
			
			$svgOutputLineStrlen = strlen($svgOutputLine);
			for ($c = 0; $c < $svgOutputLineStrlen; $c++) {
				
				if ($printedCharacterCount >= 40) break;
				
				//add blanking to first eight chars of header
				if ($ttiOutputLineIndex == 0 && ($c >= 0 && $c < 8)) 
				{
					$svgBackgroundOutput .= SVG_SPACE_CHAR;			
					$svgForegroundOutput .= "X";
					$printedCharacterCount++;
					continue;
				}
				
				$svgOutputLineCharacter = $svgOutputLine[$c];
				
				if ($svgOutputLineCharacter == '')
				{				
					switch ($svgOutputLine[$c + 1]) 
					{
						//double height
						case "M":
							$doubleHeightFlag = true;
							break;
						
						//hold graphics
						case "^":
							$holdGraphicsFlag = true;
							$holdMosaicCharacter = ttiTextToSixel($lastMosaicCharacter, $separatedGraphicsModeFlag);
							break;
						//release graphics
						case "_":
							$holdGraphicsFlag = false;
							break;
							
						//contiguous graphics
						case "Y":
							$separatedGraphicsModeFlag = false;
							break;
						//separated graphics
						case "Z":
							$separatedGraphicsModeFlag = true;
							break;
					}
					
					$svgForegroundOutput .= $lastControlCharacter == "_" ? SVG_SPACE_CHAR : ($holdMosaicCharacter ?? SVG_SPACE_CHAR);
					
					switch ($svgOutputLine[$c + 1])
					{
						case "P":
							$mostRecentColour = "black";
							$svgForegroundOutput .= '</tspan><tspan fill="black">';
							$graphicsModeFlag = true;
							break;
						case "Q":
							$mostRecentColour = "red";
							$svgForegroundOutput .= '</tspan><tspan fill="red">';
							$graphicsModeFlag = true;
							break;
						case "R":
							$mostRecentColour = "lime";
							$svgForegroundOutput .= '</tspan><tspan fill="lime">';
							$graphicsModeFlag = true;
							break;
						case "S":
							$mostRecentColour = "yellow";
							$svgForegroundOutput .= '</tspan><tspan fill="yellow">';
							$graphicsModeFlag = true;
							break;
						case "T":
							$mostRecentColour = "blue";
							$svgForegroundOutput .= '</tspan><tspan fill="blue">';
							$graphicsModeFlag = true;
							break;
						case "U":
							$mostRecentColour = "magenta";
							$svgForegroundOutput .= '</tspan><tspan fill="magenta">';
							$graphicsModeFlag = true;
							break;
						case "V":
							$mostRecentColour = "cyan";
							$svgForegroundOutput .= '</tspan><tspan fill="cyan">';
							$graphicsModeFlag = true;
							break;
						case "W":
							$mostRecentColour = "white";
							$svgForegroundOutput .= '</tspan><tspan fill="white">';
							$graphicsModeFlag = true;
							break;
							
							
						case "@":
							$mostRecentColour = "black";
							$svgForegroundOutput .= '</tspan><tspan fill="black">';
							$graphicsModeFlag = false;
							$separatedGraphicsModeFlag = false;
							$holdMosaicCharacter = null;
							$lastMosaicCharacter = null;
							break;
						case "A":
							$mostRecentColour = "red";
							$svgForegroundOutput .= '</tspan><tspan fill="red">';
							$graphicsModeFlag = false;
							$separatedGraphicsModeFlag = false;
							$holdMosaicCharacter = null;
							$lastMosaicCharacter = null;
							break;
						case "B":
							$mostRecentColour = "lime";
							$svgForegroundOutput .= '</tspan><tspan fill="lime">';
							$graphicsModeFlag = false;
							$separatedGraphicsModeFlag = false;
							$holdMosaicCharacter = null;
							$lastMosaicCharacter = null;
							break;
						case "C":
							$mostRecentColour = "yellow";
							$svgForegroundOutput .= '</tspan><tspan fill="yellow">';
							$graphicsModeFlag = false;
							$separatedGraphicsModeFlag = false;
							$holdMosaicCharacter = null;
							$lastMosaicCharacter = null;
							break;
						case "D":
							$mostRecentColour = "blue";
							$svgForegroundOutput .= '</tspan><tspan fill="blue">';
							$graphicsModeFlag = false;
							$separatedGraphicsModeFlag = false;
							$holdMosaicCharacter = null;
							$lastMosaicCharacter = null;
							break;
						case "E":
							$mostRecentColour = "magenta";
							$svgForegroundOutput .= '</tspan><tspan fill="magenta">';
							$graphicsModeFlag = false;
							$separatedGraphicsModeFlag = false;
							$holdMosaicCharacter = null;
							$lastMosaicCharacter = null;
							break;
						case "F":
							$mostRecentColour = "cyan";
							$svgForegroundOutput .= '</tspan><tspan fill="cyan">';
							$graphicsModeFlag = false;
							$separatedGraphicsModeFlag = false;
							$holdMosaicCharacter = null;
							$lastMosaicCharacter = null;
							break;
						case "G":
							$mostRecentColour = "white";
							$svgForegroundOutput .= '</tspan><tspan fill="white">';
							$graphicsModeFlag = false;
							$separatedGraphicsModeFlag = false;
							$holdMosaicCharacter = null;
							$lastMosaicCharacter = null;
							break;
						
						//new background
						case "]":
							$svgBackgroundOutput .= '</tspan><tspan fill="' . $mostRecentColour . '">';
							$backgroundCharacter = SVG_BACKGROUND_CHAR;
							$holdMosaicCharacter = null;
							break;
						// black background
						case "\\":
							$svgBackgroundOutput .= '</tspan><tspan>';
							$backgroundCharacter = null;
							break;
							
						//flash
						case "H":
							if ($unclosedTspansCount > 0) {
								$svgForegroundOutput .= '</tspan>';
								$unclosedTspansCount--;
							}
							$svgForegroundOutput .= $lastUnclosedTspanType == "conceal" ? '<tspan class="flash conceal hidden">' : '<tspan class="flash">';
							$unclosedTspansCount++;
							$lastUnclosedTspanType = "flash";
							break;
						case "I":
							if ($unclosedTspansCount > 0) {
								$svgForegroundOutput .= '</tspan>';
								$unclosedTspansCount--;
							}
							$lastUnclosedTspanType = "steady";
							break;
						//conceal
						case "X":
							$svgForegroundOutput .= '<tspan class="conceal hidden">';
							$unclosedTspansCount++;
							$lastUnclosedTspanType = "conceal";
							break;
					}
					
					$svgBackgroundOutput .= $backgroundCharacter ?? SVG_SPACE_CHAR;
					$printedCharacterCount++;
					
					$lastControlCharacter = $svgOutputLine[$c + 1];
					
					$c++;
					
					continue;
				}
				else
				{
					$lastNonControlCharacter = $svgOutputLineCharacter;
					if ($graphicsModeFlag && !preg_match("/[A-Z_\\\[\]\^]/", $svgOutputLineCharacter)) $lastMosaicCharacter = $svgOutputLineCharacter; //ignore if blastthrough
				}
				
				if ($holdGraphicsFlag == true) 
				{
					$holdMosaicCharacter = ttiTextToSixel($lastMosaicCharacter, $separatedGraphicsModeFlag);
				}
				else 
				{
					$holdMosaicCharacter = null;
				}
				
				if ($graphicsModeFlag == true) 
				{
					$svgBackgroundOutput .= $backgroundCharacter ?? SVG_SPACE_CHAR;
					$svgForegroundOutput .= ttiTextToSixel($svgOutputLineCharacter, $separatedGraphicsModeFlag);
					$printedCharacterCount++;
				}
				else 
				{
					$svgBackgroundOutput .= $backgroundCharacter ?? SVG_SPACE_CHAR;
					if ($svgOutputLineCharacter == " ") 
					{
						$svgForegroundOutput .= SVG_SPACE_CHAR;
					}
					else 
					{
						$svgForegroundOutput .= $svgOutputLineCharacter;
						//$printedCharacterCount++;
					}
					$printedCharacterCount++;
				}
			}
			
			
			$svgForegroundOutput = preg_replace("/#(?![0-9A-Z]*\;)/", "£", $svgForegroundOutput);
			
			$svgForegroundOutput = str_replace("`", "—", $svgForegroundOutput);
			$svgForegroundOutput = str_replace("_", "#", $svgForegroundOutput);
			$svgForegroundOutput = str_replace("", "", $svgForegroundOutput);
			$svgForegroundOutput = str_replace("~", "÷", $svgForegroundOutput);
			
			$svgForegroundOutput = str_replace("[", "←", $svgForegroundOutput);
			$svgForegroundOutput = str_replace("\\", "½", $svgForegroundOutput);
			$svgForegroundOutput = str_replace("]", "→", $svgForegroundOutput);
			$svgForegroundOutput = str_replace("^", "↑", $svgForegroundOutput);
			
			$svgForegroundOutput = str_replace("{", "¼", $svgForegroundOutput);
			$svgForegroundOutput = str_replace("|", "‖", $svgForegroundOutput);
			$svgForegroundOutput = str_replace("}", "¾", $svgForegroundOutput);
			
			$svgForegroundOutput = preg_replace("/&(?!#[A-Z0-9]*\;)+/", "&amp;", $svgForegroundOutput);
			$svgForegroundOutput = preg_replace("/<(?!\/?tspan|\/?text)/", "&lt;", $svgForegroundOutput);
			
			if ($ttiOutputLineIndex == 0) {
				//identify elements of header template				
				
				$svgForegroundOutput = str_replace("XXXXXXXX", '<tspan id="searchPageNumber" fill="white">XXXXXXXX</tspan>', $svgForegroundOutput);
				
				$svgForegroundOutput = str_replace("%%#", '<tspan id="currentPageNumber">%%#</tspan>', $svgForegroundOutput);
				$svgForegroundOutput = str_replace("%%£", '<tspan id="currentPageNumber">%%#</tspan>', $svgForegroundOutput);
				$svgForegroundOutput = str_replace("%%_", '<tspan id="currentPageNumber">%%#</tspan>', $svgForegroundOutput);
				$svgForegroundOutput = str_replace("mpp", '<tspan id="currentPageNumber">%%#</tspan>', $svgForegroundOutput);
				
				$svgForegroundOutput = preg_replace("/(?<!hi)dd/", '<tspan id="ee">%e</tspan>', $svgForegroundOutput); //clashed when introducing conceal hidden to tspans (because "dd" of "hidden" matched!
				$svgForegroundOutput = str_replace("%d", '<tspan id="dd">%d</tspan>', $svgForegroundOutput);
				$svgForegroundOutput = str_replace("%e", '<tspan id="ee">%e</tspan>', $svgForegroundOutput);
				
				$svgForegroundOutput = str_replace("%m", '<tspan id="mm">%m</tspan>', $svgForegroundOutput);
				
				$svgForegroundOutput = str_replace("%y", '<tspan id="yy">%y</tspan>', $svgForegroundOutput);
				
				$svgForegroundOutput = str_replace("%%a", '<tspan id="day">%%a</tspan>', $svgForegroundOutput);
				$svgForegroundOutput = str_replace("DAY", '<tspan id="day">%%a</tspan>', $svgForegroundOutput);
				
				$svgForegroundOutput = str_replace("%%b", '<tspan id="month">%%b</tspan>', $svgForegroundOutput);
				$svgForegroundOutput = str_replace("MTH", '<tspan id="month">%%b</tspan>', $svgForegroundOutput);
				
				$svgForegroundOutput = str_replace("%H", '<tspan id="hours">%H</tspan>', $svgForegroundOutput);
				$svgForegroundOutput = str_replace("hh", '<tspan id="hours">%H</tspan>', $svgForegroundOutput);
				
				$svgForegroundOutput = str_replace("%M", '<tspan id="minutes">%M</tspan>', $svgForegroundOutput);
				$svgForegroundOutput = str_replace("nn", '<tspan id="minutes">%M</tspan>', $svgForegroundOutput);
				
				$svgForegroundOutput = str_replace("%S", '<tspan id="seconds">%S</tspan>', $svgForegroundOutput);
				
				$svgForegroundOutput = preg_replace("/(?<!cla)ss/", '<tspan id="seconds">%S</tspan>', $svgForegroundOutput); //clashed when introducing class to tspans (because "ss" of "class" matched!
				
				$svgForegroundOutput = preg_replace("/(?<=<\/tspan>)([^<]+)/", '<tspan>$1</tspan>', $svgForegroundOutput);
			}
			
			//world clock			
			$svgForegroundOutput = preg_replace("/%t([+-][0-9]{2})/", '<tspan class="world-clock-hours" data-time-shift="$1">HH</tspan>:<tspan class="world-clock-minutes">MM</tspan>', $svgForegroundOutput);
			
			//system time			
			$svgForegroundOutput = str_replace("%%%%%%%%%%%%timedate", '<tspan class="system-time-date"></tspan>', $svgForegroundOutput);
			
			$y = SVG_LINE_HEIGHT * (SVG_Y_OFFSET + $ttiOutputLineIndex);
			
			if ($doubleHeightFlag) 
			{		
				$suppressNextLineFlag = true;
				return '		<text '. ($ttiOutputLineIndex != 0 ? 'class="double-height" ' : '') . ($ttiOutputLineIndex == 0 ? 'class="header-row" ' : '') . 'x="' . SVG_X_OFFSET . '" y="' . ($y / 2) . '" data-output-line="' . $ttiOutputLineIndex . '" data-layer="background"><tspan>' . $svgBackgroundOutput . '</tspan></text>'
			 . '		<text '. ($ttiOutputLineIndex != 0 ? 'class="double-height" ' : '') . ($ttiOutputLineIndex == 0 ? 'class="header-row" ' : '') . 'x="' . SVG_X_OFFSET . '" y="' . ($y / 2) . '" data-output-line="' . $ttiOutputLineIndex . '" data-layer="foreground"><tspan>' . $svgForegroundOutput . '</tspan>' . str_repeat('</tspan>', $unclosedTspansCount) . '</text>';
			}
			
			return '		<text ' . ($ttiOutputLineIndex == 0 ? 'class="header-row" ' : '') . 'x="' . SVG_X_OFFSET . '" y="' . $y . '" data-output-line="' . $ttiOutputLineIndex . '" data-layer="background"><tspan>' . $svgBackgroundOutput . '</tspan></text>' . PHP_EOL
			 . '		<text ' . ($ttiOutputLineIndex == 0 ? 'class="header-row" ' : '') . 'x="' . SVG_X_OFFSET . '" y="' . $y . '" data-output-line="' . $ttiOutputLineIndex . '" data-layer="foreground"><tspan>' . $svgForegroundOutput . '</tspan>' . str_repeat('</tspan>', $unclosedTspansCount) . '</text>'. PHP_EOL;
		}
	}
	
	if (!function_exists('ttiTextToSixel')) 
	{
		function ttiTextToSixel($ttiCharacter, $separatedGraphicsModeFlag) 
		{
			$shiftValue = $separatedGraphicsModeFlag ? 59008 : 58976;
			
			if ($ttiCharacter != "") {
				if (preg_match("/[A-Z_\\\[\]\^]/", $ttiCharacter)) return $ttiCharacter; //blastthrough
				$unicodeValue = mb_ord($ttiCharacter, "UTF-8");
				return mb_chr($unicodeValue + $shiftValue, "UTF-8");
			}
			
			return null;
		}
	}
	
	if (!function_exists('ttiOutputLineToTextOnly')) 
	{
		function ttiOutputLineToTextOnly($ttiOutputLineIndex, $ttiOutputLine) {
			return preg_replace([
				"/(?:[\x{001b}][QRSTUVW]).+(?:[\x{001b}][ABCDEFG])?/",
				"/[\x{001b}].?/",
				"/\s*$/",
				"/\n|\r/",
				"/^\s*/",
				"/\s{2,}/",
				"/[^0-9A-Za-z\s]{3,}/"
			], " ", str_replace(array("&","<",">"), array("\\\&","\\\<","\\\>"), $ttiOutputLine));
		}
	}
?>

<svg class="teletext-svg" id="<?php echo $randomId; ?>" viewBox="0 0 <?php echo SVG_BACKGROUND_WIDTH; ?> <?php echo SVG_BACKGROUND_HEIGHT; ?>" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
	<style>
		@font-face {
			font-family: 'xbmc_teletext';
			src: url("data:font/woff2;base64,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pMceZ6WDhyfHW3heQqQJv9GpVm9yMFr846l+vj5Ox8+w0+gih6fQn2L87H6Mr5NYsO7L/0fNw6htCKsr6H6IFPAUevDQXV/SrT4IIFiDQJ+bD64KAAB01ahnAwAA0JXyKW1iN9QAAAAAAEBERERERERERFVVVVVVVVWVJEmSJEnSzMzMzMzMzMzd3d3d3d3dHR4AAAAAAECSJEmSJClJkiRJkmTbtm3btu11VEoppZRSSimllNJaa6211lprrbU2xhhjjDHGGGOMsdZaa6211lprbezMJxUgMS6k43p9SptSAAAAAAAAAACIiIiIiIiIiERERERERERE9Qi8zvNNBkiMC+m4Xp/SJnYjAwAAAAAAAABERERERERERCIiIiIiIiIixhhjjDHGGGOMMc4555xzzjnnnHMhhBBCCCGEEEIIKaWUUkoppZRSSvEAAAAAAACSJEmSJElJkiRJkiTbtm3btm3dV0oppZRSSimllNJaa6211lprrbU2xhhjjDHGGGOMsdZaa6211lprbezcJxUgMS6k43p9SptSAAAAAAAAAACIiIiIiIiIiEREREREREREjDHGGGOMMcYYY5xzzjnnnHPOOedCCCGEEEIIIYQQUkoppZRSSimllOIBAAAAAAAkSZIkSZKSJEmSJEm2bdu2bdvwpZRSSimllFJKKa211lprrbXWWmtjjDHGGGOMMcaYtqkAiXEhHdfrU9q8Cw==") format("woff2");
			//src: url('/teletext/teletext.woff2') format('woff2'),
			//	 url('/teletext/teletext.woff') format('woff');
		}
	
		text {
			font-family: "xbmc_teletext";
			font-size: <?php echo SVG_FONT_SIZE; ?>px;
			letter-spacing: <?php echo SVG_LETTER_SPACING; ?>px;
			text-rendering: optimizeSpeed;
			/* text-rendering: geometricPrecision; */
			dominant-baseline: hanging;
		}
		
		text.double-height {
			transform: scaleY(2) translateY(0.05em);
		}
		
		g.subpage {
			fill: white;
			display: none;
		}
		
		g.subpage.current-subpage {
			display: unset;
		}
		
		g.resizable-lines.hidden * {
			visibility: hidden !important;
		}
		
		g.resizable-lines.top-half.resize {
			transform: scaleY(2) translateY(-42px);
		}
		
		g.resizable-lines.bottom-half.resize {
			transform: scaleY(2) translateY(-1196px);
		}
		
		rect.background, rect.subpage-background {
			fill: black;
		}
		
		@keyframes flash {
			50% {
				opacity: 0;
			}
		}
		
		tspan.flash {
			animation: flash 1s step-start infinite;
		}
		
		tspan.conceal {
			visibility: visible;
		}
		
		tspan.conceal.hidden {
			visibility: hidden;
		}
	</style>
	
	<rect x="0" y="0" width="<?php echo SVG_BACKGROUND_WIDTH; ?>" height="<?php echo SVG_BACKGROUND_HEIGHT; ?>" class="background"></rect>
	
	<?php
	
		//if (!is_null($requestedService) && ctype_xdigit($requestedPage) && isset($subpages))
		if (!empty($subpages))
		{
			$subpagesCount = $headerOnly ? 1 : count($subpages);
			for ($s = 0; $s < $subpagesCount; $s++) 
			{	
				$outputSubpage = $outputSubpages[$s];
				$outputSubpageOutputLines = $outputSubpage->getOutputLines();
				
				$maxLines = $headerOnly ? 1 : count($outputSubpageOutputLines);
				
				echo '<g class="subpage" data-subpage-index="' . $s .'" data-subpage-cycle-time="' . $outputSubpage->getCycleTime() . '" '
					. ' data-fastext-red="' . $outputSubpage->getFastextLink("red") . '"'
					. ' data-fastext-green="' . $outputSubpage->getFastextLink("green") . '"'
					. ' data-fastext-yellow="' . $outputSubpage->getFastextLink("yellow") . '"'
					. ' data-fastext-blue="' . $outputSubpage->getFastextLink("blue") . '"'
					. ' data-fastext-index="' . $outputSubpage->getFastextLink("index") . '"'
					.">\n";
					
					echo "		<title>";
						for ($l = 1; $l < $maxLines - 1; $l++) 
						{
							echo '				' . ttiOutputLineToTextOnly($l, $outputSubpageOutputLines[$l]) . "\n";
						}
					echo "		</title>\n";
					
					echo '		<rect x="0" y="0" width="'. SVG_BACKGROUND_WIDTH .'" height="'. SVG_BACKGROUND_HEIGHT .'" class="subpage-background"></rect>' . "\n";
					
					echo '		<g class="resizable-lines top-half' . ($resize == '1' ? ' resize' : '') . ($resize == '2' ? ' hidden' : '') . '">';
					for ($l = 0; $l < $maxLines && $l <= 11; $l++) 
					{
						if ($l != 24) echo '' . ttiOutputLineToSvg($l, $outputSubpageOutputLines[$l]);
					}
					echo '		</g>';
					echo '		<g class="resizable-lines bottom-half' . ($resize == '1' ? ' hidden' : '') . ($resize == '2' ? ' resize' : '') . '">';
					for ($l = 12; $l < $maxLines && $l <= 23; $l++) 
					{
						echo '' . ttiOutputLineToSvg($l, $outputSubpageOutputLines[$l]);
					}
					echo '		</g>';
					if ($maxLines >= 24) echo '' . ttiOutputLineToSvg(24, $outputSubpageOutputLines[24]);
				
				echo "	</g>\n";
			}
			
			
		}
	
	?>
	
	<script>
		//<![CDATA[ 				
			var threeLetterWeekday = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
			var threeLetterMonth = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
		
			var updateAllTimesDatesInterval = null;
			var headerRowTextElements = document.querySelectorAll("svg.teletext-svg text.header-row[data-layer='foreground']");
			var headerRowTextElementsLength = headerRowTextElements.length;
			
			function initialSubpageIndex(totalLength, timePeriod)
			{
				let date = new Date();
				let time = date.getTime();
				
				let timeSpan = time / timePeriod;
				timeSpan = timeSpan.toFixed(0);
				
				let index = timeSpan % totalLength;
				return index;
			}
			
			function updateWorldClockTimes() {				
				let svg = document.querySelectorAll("svg[id='<?php echo $randomId; ?>']")[0];
				let worldClockHours = svg.querySelectorAll("tspan.world-clock-hours");
				let worldClockMinutes = svg.querySelectorAll("tspan.world-clock-minutes");
				
				for (let i = 0; i < worldClockHours.length; i++) {
					let timeshift = worldClockHours[i].dataset.timeShift; // in half hours!
					
					let direction = timeshift.substr(0, 1);
					let halfhourshift = parseInt(timeshift.substr(1, 2));
					
					let newTime = new Date();
					
					if (direction == "+") newTime.setMinutes(newTime.getMinutes() + (30 * halfhourshift));
					if (direction == "-") newTime.setMinutes(newTime.getMinutes() - (30 * halfhourshift));
					
					worldClockHours[i].innerHTML = ("0" + newTime.getUTCHours()).substr(-2);
					worldClockMinutes[i].innerHTML = ("0" + newTime.getUTCMinutes()).substr(-2);
				}
			}
			
			function updateSystemTimeDate() {
				let date = new Date();
				let yy = ("0" + date.getYear()).substr(-2);
				let mm = ("0" + (date.getMonth() + 1)).substr(-2);
				let dd = ("0" + date.getDate()).substr(-2);
				let ee = (" " + date.getDate()).substr(-2);
				let hours = ("0" + date.getHours()).substr(-2);
				let minutes = ("0" + date.getMinutes()).substr(-2);
				let seconds = ("0" + date.getSeconds()).substr(-2);
				
				let svg = document.querySelectorAll("svg[id='<?php echo $randomId; ?>']")[0];
				let systemTimeDate = svg.querySelectorAll("tspan.system-time-date");
				systemTimeDate[0].innerHTML = threeLetterWeekday[date.getDay()] + " " + dd + " " + threeLetterMonth[date.getMonth()] + " " + hours + ":" + minutes + "/" + seconds;
			}
		
			function updateAllTimesDates() {
				let date = new Date();
				let yy = ("0" + date.getYear()).substr(-2);
				let mm = ("0" + (date.getMonth() + 1)).substr(-2);
				let dd = ("0" + date.getDate()).substr(-2);
				let ee = (" " + date.getDate()).substr(-2);
				let hours = ("0" + date.getHours()).substr(-2);
				let minutes = ("0" + date.getMinutes()).substr(-2);
				let seconds = ("0" + date.getSeconds()).substr(-2);
				
				let headerRowTextElementsLength = headerRowTextElements.length;
				for (i = 0; i < headerRowTextElementsLength; i++) {
					let targetSvgHeaderRowTextElement = headerRowTextElements[i];
				
					let hoursTspanElement = targetSvgHeaderRowTextElement.querySelectorAll("tspan#hours")[0];
					let minutesTspanElement = targetSvgHeaderRowTextElement.querySelectorAll("tspan#minutes")[0];
					let secondsTspanElement = targetSvgHeaderRowTextElement.querySelectorAll("tspan#seconds")[0];
					
					let yyTspanElement = targetSvgHeaderRowTextElement.querySelectorAll("tspan#yy")[0];
					let mmTspanElement = targetSvgHeaderRowTextElement.querySelectorAll("tspan#mm")[0];
					let ddTspanElement = targetSvgHeaderRowTextElement.querySelectorAll("tspan#dd")[0];
					
					let eeTspanElement = targetSvgHeaderRowTextElement.querySelectorAll("tspan#ee")[0];
					
					let dayTspanElement = targetSvgHeaderRowTextElement.querySelectorAll("tspan#day")[0];
					let monthTspanElement = targetSvgHeaderRowTextElement.querySelectorAll("tspan#month")[0];
					
					if (hoursTspanElement !== undefined) hoursTspanElement.innerHTML = hours;
					if (minutesTspanElement !== undefined) minutesTspanElement.innerHTML = minutes;
					if (secondsTspanElement !== undefined) secondsTspanElement.innerHTML = seconds;
					
					if (yyTspanElement !== undefined) yyTspanElement.innerHTML = yy;
					if (mmTspanElement !== undefined) mmTspanElement.innerHTML = mm;
					if (ddTspanElement !== undefined) ddTspanElement.innerHTML = dd;
					
					if (eeTspanElement !== undefined) eeTspanElement.innerHTML = ee;
					
					if (dayTspanElement !== undefined) dayTspanElement.innerHTML = threeLetterWeekday[date.getDay()];
					if (monthTspanElement !== undefined) monthTspanElement.innerHTML = threeLetterMonth[date.getMonth()];				
				}
			}
			
			updateAllTimesDates();
			clearInterval(updateAllTimesDatesInterval);
			updateAllTimesDatesInterval = setInterval(function () { updateAllTimesDates(); }, 500);
			
			function updateCurrentPageNumber(svgId) {
				let svg = document.querySelectorAll("svg[id='<?php echo $randomId; ?>']")[0];
				let headerRowTextElements = svg.querySelectorAll("text.header-row");
				
				let headerRowTextElementsLength = headerRowTextElements.length;
				for (i = 0; i < headerRowTextElementsLength; i++) {
					let targetSvgHeaderRowTextElement = headerRowTextElements[i];
					
					let currentPageNumberTspanElement = targetSvgHeaderRowTextElement.querySelectorAll("tspan#currentPageNumber")[0];
					if (currentPageNumberTspanElement !== undefined) currentPageNumberTspanElement.innerHTML = "<?php echo $requestedPage; ?>";
					
					let searchPageNumberTspanElement = targetSvgHeaderRowTextElement.querySelectorAll("tspan#searchPageNumber")[0];
					if (searchPageNumberTspanElement !== undefined) searchPageNumberTspanElement.innerHTML = "P" + "<?php echo $requestedPage; ?>" + "&#160;&#160;&#160;&#160;";
				}
			}
			
			updateCurrentPageNumber("<?php echo $randomId; ?>");
			
			var pageRotationTimeout = null;
			
			function pageRotation(svgId, subpageIndex, loop) {
				let svg = document.querySelectorAll("svg[id='" + svgId + "']")[0];
				let subpages = svg.querySelectorAll("g.subpage");
				let numberOfSubpages = subpages.length;
				
				for (let s = 0; s < numberOfSubpages; s++) {
					subpages[s].classList.remove("current-subpage");
				}
				
				let currentSubpage = subpages[subpageIndex];
				currentSubpage.classList.add("current-subpage");
				let cycleTime = currentSubpage.dataset.subpageCycleTime * 1000;
				let nextSubpageIndex = subpageIndex + 1;
				if (nextSubpageIndex >= numberOfSubpages) nextSubpageIndex = 0;
				
				if (loop) {
					pageRotationTimeout = setTimeout(function() { pageRotation(svgId, nextSubpageIndex, true); }, cycleTime);
				}
				else {
					clearTimeout(pageRotationTimeout);
				}
				
				updateWorldClockTimes();
				updateSystemTimeDate();
				
				if (typeof externalUpdateCurrentSubpageIndex === "function") externalUpdateCurrentSubpageIndex(subpageIndex, numberOfSubpages);
			}
			
			<?php
			
				if (!empty($subpages))
				{
					if (isset($requestedSubpage))
					{
						echo 'pageRotation("' . $randomId . '", ' . $requestedSubpage . ', false);';
					}
					//else if ($headerOnly || count($subpages) == 1)
					else if ($headerOnly) // loop anyway, for world clock to run, even if subpage count is 1 
					{
						echo 'pageRotation("' . $randomId . '", 0, false);';
					}
					else
					{
						echo 'pageRotation("' . $randomId. '", initialSubpageIndex(' . count($subpages) . ', ' . (($outputSubpages[0])->getCycleTime()) * 1000 .'), true);';
					}
				}
			
			?>
			
		//]]>
	</script>
</svg>