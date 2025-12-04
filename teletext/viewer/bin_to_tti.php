<?php
	try 
	{		
		$service = isset($_GET['service']) ? trim($_GET['service']) : "";
		$recovery = isset($_GET['recovery']) ? trim($_GET['recovery']) : null;
		$page = isset($_GET['page']) ? trim($_GET['page']) : "";
		$binToTtiEcho = isset($binToTtiEcho) ? $binToTtiEcho : true;
		
		if ($binToTtiEcho)
		{
			header("Content-Type: text/plain; charset=utf-8");
			header("Content-Disposition: attachment; filename=P" . $page . ".tti");
		}
		
		include("../inc/packet.php");
		include("../inc/t42_page.php");
		
		function decodePacketData($data)
		{
			$binaryData = "";
			$returnData = "";
			
			for ($i = 0; $i < strlen($data); $i++)
			{
				$binaryData .= "0" . substr(str_pad(decbin(ord($data[$i])), 8, 0, STR_PAD_LEFT), 1, 7);
			}
			
			for ($i = 0; $i < strlen($binaryData); $i = $i + 8)
			{
				$bindec = bindec(substr($binaryData, $i, 8));
				if ($bindec < 32) 
				{
					$bindec = $bindec + 64;
					$returnData .= chr(27);
				}
				
				$returnData .= chr($bindec);
			}
			
			return $returnData;
		}
		
		//$binFilename = "../services/" . $service . "/" . $page . ".bin";
		if ($recovery) 
		{
			$binFiles = glob("../recoveries/" . $recovery . "/*.bin");
		}
		else 
		{
			$binFiles = glob("../services/" . $service . "/*.bin");
		}
		$matchingBinFile = null;
		
		foreach ($binFiles as $file)
		{
			$basenameFile = basename($file);
			preg_match_all("/(" . $page . ").*\.bin/i", basename($file), $matches);
			
			if ($matches[1])
			{
				$matchingBinFile = $file;
				break;
			}
		}		
		
		$binFile = fopen($matchingBinFile, "rb");
		
		$packets = [];
		$t42Pages = [];
		
		while(!feof($binFile)) 
		{
			$packet = new Packet();
			$packet->setMRAG(fread($binFile, 2));
			$packet->setData(fread($binFile, 40));
			
			array_push($packets, $packet);
			//array_push($binFileLines, fread($binFile, 42));
		}
		
		fclose($binFile);
		/*
		for ($i = 0; $i < count($packets); $i++)
		{
			echo $packets[$i]->getMagazineNumber() . ", ";
			echo $packets[$i]->getPacketNumber() . ": ";
			echo $packets[$i]->getDecodedData() . "\n";
		}
		*/
		
		$t42Page = new T42Page($packets[0]->getMagazineNumber());
		
		for ($i = 0; $i < count($packets); $i++)
		{
			$packet = $packets[$i];
			
			if ($packet->getPacketNumber() == 0)
			{
				if ($i != 0)
				{
					array_push($t42Pages, $t42Page);
					$t42Page = new T42Page($packet->getMagazineNumber());
				}
			}
			
			$t42Page->setMRAG($packet->getPacketNumber(), $packet->getMRAG());
			$t42Page->setPacket($packet->getPacketNumber(), $packet->getData());
		}
		
		$binToTtiOutputString = "";
		
		for ($i = 0; $i < count($t42Pages); $i++)
		{
			$t42Page = $t42Pages[$i];
			
			$binToTtiOutputString .= "PN," . $t42Page->getPageAddress() . str_pad($i + 1, 2, 0, STR_PAD_LEFT) . "\n"; //consider changing to actual subpage number from file
			$binToTtiOutputString .= "SC," . str_pad($i + 1, 4, 0, STR_PAD_LEFT) . "\n";
			$binToTtiOutputString .= "PS,8000\n";
			$binToTtiOutputString .= "CT,12,T\n";
			
			for ($l = 0; $l <= 24; $l++)
			{
				$decodedPacketData = decodePacketData($t42Page->getPacket($l));
				//if ($l == 0) $decodedPacketData = "        " . substr($decodedPacketData, 14);
				if ($l == 0)
				{
					$decodedPacketDataLength = strlen($decodedPacketData);
					$decodedPacketDataLengthDifference = $decodedPacketDataLength - 53;
					$decodedPacketData = "        " . substr($decodedPacketData, 14 + $decodedPacketDataLengthDifference);
				}
				$binToTtiOutputString .= "OL," . $l . "," . $decodedPacketData . "\n";
			}
			
			//add fastext links here from X/27
			$fastextLinks = $t42Page->getFastextLinks();
			
			if ($fastextLinks != null) $binToTtiOutputString .= "FL," . implode(",",$fastextLinks) . "\n";
		}
		
		if ($binToTtiEcho) echo $binToTtiOutputString;
		
		//exit();
	}
	catch (Exception $e)
	{
		http_response_code(404);
	}
?>