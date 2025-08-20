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
		
		if (!class_exists('Packet'))
		{
			class Packet 
			{
				public $mrag;
				public $data;
				
				function getMRAG() 
				{
					return $this->mrag;
				}
				
				function setMRAG($mrag) 
				{
					$this->mrag = $mrag;
				}
				
				function getData() 
				{
					return $this->data;
				}
				
				function setData($data) 
				{
					$this->data = $data;
				}
				
				function getBinaryMRAG()
				{
					$binaryMRAG = "";
					for ($i = 0; $i < strlen($this->mrag); $i++)
					{
						$binaryMRAG .= str_pad(decbin(ord($this->mrag[$i])), 8, 0, STR_PAD_LEFT);
					}
					
					return $binaryMRAG;
				}
				
				function getMagazineNumber() 
				{
					$binaryMRAG = $this->getBinaryMRAG();
					$binaryMRAGmagazine = substr($binaryMRAG, 2, 1) . substr($binaryMRAG, 4, 1) . substr($binaryMRAG, 6, 1);
					
					$magazineNumber = bindec($binaryMRAGmagazine);
					if ($magazineNumber == 0) $magazineNumber = 8;
					
					return $magazineNumber;
				}
				
				function getPacketNumber()
				{
					$binaryMRAG = $this->getBinaryMRAG();
					$binaryMRAGpacket = substr($binaryMRAG, 8, 1) . substr($binaryMRAG, 10, 1) . substr($binaryMRAG, 12, 1) . substr($binaryMRAG, 14, 1) . substr($binaryMRAG, 0, 1);
					
					return bindec($binaryMRAGpacket);
				}
			}
		}
		
		if (!class_exists('T42Page'))
		{
			class T42Page 
			{
				public $magazineNumber;
				public $mrags;
				public $packets;
				
				public function __construct($magazineNumber)
				{
					$this->magazineNumber = $magazineNumber;
					$this->mrags = array_fill(0, 32, null);
					$this->packets = array_fill(0, 32, null);
				}
				
				public function getMRAG($packetNumber)
				{
					return $this->mrags[$packetNumber];
				}
				
				public function setMRAG($packetNumber, $mragData)
				{
					$this->mrags[$packetNumber] = $mragData;
				}
				
				public function getPacket($packetNumber)
				{
					return $this->packets[$packetNumber];
				}
				
				public function setPacket($packetNumber, $packetData)
				{
					$this->packets[$packetNumber] = $packetData;
				}
				
				public function getPageAddress()
				{
					$headerPacket = $this->getPacket(0);
										
					$binaryUnits = str_pad(decbin(ord($headerPacket[0])), 8, 0, STR_PAD_LEFT);
					$binaryTens = str_pad(decbin(ord($headerPacket[1])), 8, 0, STR_PAD_LEFT);
					
					$binaryTensNoEEC = substr($binaryTens, 0, 1) . substr($binaryTens, 2, 1) . substr($binaryTens, 4, 1) . substr($binaryTens, 6, 1);
					$binaryUnitsNoEEC = substr($binaryUnits, 0, 1) . substr($binaryUnits, 2, 1) . substr($binaryUnits, 4, 1) . substr($binaryUnits, 6, 1);
					
					return $this->magazineNumber . dechex(bindec($binaryTensNoEEC)) . dechex(bindec($binaryUnitsNoEEC));
				}
				
				public function getFastextLinks()
				{
					$fastextPacket = $this->getPacket(27);
					$fastextLinkAddresses = [];
					
					if ($fastextPacket)
					{		
						for ($i = 0; $i <= 5; $i++)
						{
							$binaryUnits = str_pad(decbin(ord($fastextPacket[(6 * $i) + 1])), 8, 0, STR_PAD_LEFT);
							$binaryTens = str_pad(decbin(ord($fastextPacket[(6 * $i) + 2])), 8, 0, STR_PAD_LEFT);
							/*
							$binaryLinkMagazine = substr(str_pad(decbin(ord($fastextPacket[(6 * $i) + 6])), 8, 0, STR_PAD_LEFT), 7, 1)
												. substr(str_pad(decbin(ord($fastextPacket[(6 * $i) + 6])), 8, 0, STR_PAD_LEFT), 5, 1)
												. substr(str_pad(decbin(ord($fastextPacket[(6 * $i) + 4])), 8, 0, STR_PAD_LEFT), 7, 1);
												*/
												
							
							$binaryLinkMagazine = substr(str_pad(decbin(ord($fastextPacket[(6 * $i) + 6])), 8, 0, STR_PAD_LEFT), 0, 1)
												. substr(str_pad(decbin(ord($fastextPacket[(6 * $i) + 6])), 8, 0, STR_PAD_LEFT), 2, 1)
												. substr(str_pad(decbin(ord($fastextPacket[(6 * $i) + 4])), 8, 0, STR_PAD_LEFT), 0, 1);
							
							$binaryTensNoEEC = substr($binaryTens, 0, 1) . substr($binaryTens, 2, 1) . substr($binaryTens, 4, 1) . substr($binaryTens, 6, 1);
							$binaryUnitsNoEEC = substr($binaryUnits, 0, 1) . substr($binaryUnits, 2, 1) . substr($binaryUnits, 4, 1) . substr($binaryUnits, 6, 1);
							
							
							$packetMag = $this->magazineNumber;
							if ($packetMag == 8) $packetMag = 0;
							$magazineNumber = $packetMag ^ bindec($binaryLinkMagazine);					
							
							if ($magazineNumber == 0) $magazineNumber = 8;
							
							array_push($fastextLinkAddresses, $magazineNumber . strtoupper(dechex(bindec($binaryTensNoEEC))) . strtoupper(dechex(bindec($binaryUnitsNoEEC))));
							//link 0 to 3 = rgyb, link 5 = index (tallies exactly with FL row in TTI)
							//echo "fastext link " . $i . ": " . bindec($binaryMagazine) . strtoupper(dechex(bindec($binaryTensNoEEC))) . strtoupper(dechex(bindec($binaryUnitsNoEEC))) . ";; ";
						}
					}
					
					//return $this->magazineNumber . dechex(bindec($binaryTensNoEEC)) . dechex(bindec($binaryUnitsNoEEC));
					return $fastextLinkAddresses;
				}
			}
		}
		
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
				if ($l == 0) $decodedPacketData = "        " . substr($decodedPacketData, 14);
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