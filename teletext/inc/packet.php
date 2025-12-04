<?php
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
?>