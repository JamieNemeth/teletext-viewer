<?php
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
?>