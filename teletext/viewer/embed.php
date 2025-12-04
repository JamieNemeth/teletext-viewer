<?php include "constants.php"; ?>

<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
	#teletextcontainer {
		display: flex;
		align-items: center;
		justify-content: center;
		height: 100%;
		width: 100%;
	}
	
	<?php
		if (isset($_GET['fullscreen'])) 
		{
			echo <<<STR
				body {
					background: black;
					height: 100%;
					width: 100%;
					margin: 0;
				}
				
				#teletextscreen {
					height: 100%;
					width: 100%;
					background-color: black;
					aspect-ratio: 1.22;
				}
			STR;
		}
		else
		{
			echo <<<STR
				#teletextscreen {
					height: 90%;
					width: auto;
					//background-color: black;
					aspect-ratio: 1.22;
				}
				
				#teletextremote {
					background-image: linear-gradient(darkgrey, grey);
					width: 160px;
					height: 380px;
					margin: 16px;
					border-radius: 14px;
					padding-top: 30px;
				}
				
				#remotebuttoncontainer {
					width: 160px;
					display: flex;
					flex-wrap: wrap;
					justify-content: center;
				}
				
				#remoteextrabuttoncontainer {
					width: 160px;
					display: flex;
					flex-wrap: wrap;
					justify-content: center;
				}
				
				.remotebutton {
					font-family: 'Titillium Web';
					line-height: 160%;
					font-size: 100%;
					color: white;
					display: flex;
					background-image: linear-gradient(darkgrey, black);
					height: 42px;
					flex: 0 0 42px;
					margin: 2px 2px;			
					border-radius: 100%;
					border: 1px solid grey;
					justify-content: center;
					align-items: center;
					cursor: pointer;
				}
				
				.remotebuttongap {
					display: flex;
					height: 42px;
					flex: 0 0 42px;
					margin: 2px 2px;
					justify-content: center;
					align-items: center;
				}
				
				.remoteverticalspacer {
					display: flex;
					flex: 0 0 100%;
					height: 20px;
				}
				
				.fastextbutton {
					font-family: 'Titillium Web';
					line-height: 160%;
					color: white;
					display: flex;
					height: 30px;
					flex: 0 0 30px;
					margin: 2px 2px;			
					border-radius: 100%;
					border: 1px solid grey;
					justify-content: center;
					align-items: center;
					cursor: pointer;
					font-size: 0;
				}
				
				.functionbutton {
					font-family: 'Titillium Web';
					line-height: 160%;
					font-size: 100%;
					color: white;
					display: flex;
					background-image: linear-gradient(darkgrey, black);
					height: 30px;
					flex: 0 0 30px;
					margin: 2px 2px;
					padding-left: 1px;
					border-radius: 100%;
					border: 1px solid grey;
					justify-content: center;
					align-items: center;
					cursor: pointer;
				}
				
				.functionbutton[data-name='index'] {
					font-family: 'Times New Roman';
					font-weight: bold;
					font-style: italic;
				}
				
				.functionbuttongap {
					display: flex;
					height: 30px;
					flex: 0 0 30px;
					margin: 2px 2px;
					border-radius: 100%;
					border: 1px solid grey;
					justify-content: center;
					align-items: center;
					cursor: pointer;
					font-size: 0;
				}

				.remoteextrabutton {
					font-family: 'xbmc_teletext';
					color: white;
					display: flex;
					background-image: linear-gradient(darkgrey, black);
					height: 42px;
					flex: 0 0 42px;
					margin: 2px 2px;			
					border-radius: 20%;
					border: 1px solid grey;
					justify-content: center;
					align-items: center;
					cursor: pointer;
				}
				
				.remoteextrabuttongap {
					display: flex;
					height: 42px;
					flex: 0 0 42px;
					margin: 2px 2px;
					border-radius: 100%;
					border: 1px solid grey;
					justify-content: center;
					align-items: center;
					cursor: pointer;
					font-size: 0;
				}
				
				.remoteextrabutton svg {
					width: 60%;
					height: 60%;
				}
				
				
				@media screen and (max-width: 900px) and (any-hover: hover),
					screen and (max-width: 900px) and (any-hover: none) and (orientation: portrait) {
					
					#teletextcontainer {
						width: 100%;
						height: 100%;
						flex-direction: column;
					}
					
					#teletextscreen {
						width: 100%;
						height: auto;
						max-height: 70%;
						//max-width: 100%;
						//aspect-ratio: 1.22;
					}
				}
					
				@media screen and (max-width: 900px) and (any-hover: hover),
					screen and (max-height: 410px) and (any-hover: hover),
					screen and (max-width: 900px) and (any-hover: none) and (orientation: portrait),
					screen and (max-height: 600px) and (any-hover: none) and (orientation: landscape)
					{
					
					.remotebuttongap, .remoteextrabuttongap, .remoteverticalspacer {
						display: none;
					}
					
					#teletextremote {
						width: 340px;
						height: 150px;
						border-radius: 14px;
						padding-top: 6px;
						padding-left: 4px;
						padding-bottom: 6px;
					}
					
					#remotebuttoncontainer {
						width: 340px;
						display: flex;
						flex-wrap: wrap;
						justify-content: left;
					}
					
					#remoteextrabuttoncontainer {
						width: 340px;
						display: flex;
						flex-wrap: wrap;
						justify-content: right;
						margin-top: -48px;
						margin-left: -4px;
					}
					
					.fastextbutton, .functionbutton, .functionbuttongap {
						height: 42px;
						flex: 0 0 42px;
					}
				
				}
			STR;
		}
		
		if (isset($_GET['minitv'])) 
		{
			if (!isset($_GET['nobackground']))
			{
				echo <<<STR
				
				body {
					background-image: url("/teletext/viewer/assets/background.png");
					backdrop-filter: contrast(120%) brightness(40%);
				}
				
				STR;
			}
			
			echo <<<STR
				
				body {
					margin: 0px;
				}
				
				#teletextsurround {
					background-image: url("/teletext/viewer/assets/minitv.png");
					background-repeat: no-repeat;
					background-size: 90%;
					height: 98%;
					aspect-ratio: 4/3;
					background-position-y: center;
					overflow: visible;
					filter: drop-shadow(10px 10px 4px rgba(0, 0, 0, 0.4));
				}
				
				#teletextscreen {
					position: relative;
					left: 14.2%;
					top: 8.6%;
					height: 67%;
					aspect-ratio: 1.22;
				}
				
				#teletextremote {
					background-image: linear-gradient(darkgrey, grey);
					width: 160px;
					height: 380px;
					margin: 0px 16px 0px 4px;
					border-radius: 14px;
					padding-top: 30px;
					filter: drop-shadow(10px 10px 4px rgba(0, 0, 0, 0.4));
				}
				
				@media screen and (max-width: 900px) and (any-hover: hover),
					screen and (max-width: 900px) and (any-hover: none) and (orientation: portrait) {
					
					#teletextsurround {
						width: 100%;
						height: auto;
						background-position-y: center;
						background-position-x: center;
						flex-direction: column;
					}
					
					#teletextscreen {
						position: relative;
						left: 0;
						top: 12%;
						height: 61.6%;
						aspect-ratio: 1.22;
					}
				}
				
				@media screen and (max-width: 900px) and (any-hover: hover),
					screen and (max-height: 410px) and (any-hover: hover),
					screen and (max-width: 900px) and (any-hover: none) and (orientation: portrait),
					screen and (max-height: 600px) and (any-hover: none) and (orientation: landscape)
					{
					
					.remotebuttongap, .remoteverticalspacer {
						display: none;
					}
					
					#teletextremote {
						width: 340px;
						height: 150px;
						border-radius: 14px;
						padding-top: 6px;
						padding-left: 4px;
						padding-bottom: 6px;
					}
					
					#remotebuttoncontainer {
						width: 340px;
						display: flex;
						flex-wrap: wrap;
						justify-content: left;
					}
					
					#remoteextrabuttoncontainer {
						width: 340px;
						display: flex;
						flex-wrap: wrap;
						justify-content: right;
						margin-top: -48px;
						margin-left: -4px;
					}
					
					.fastextbutton, .functionbutton, .functionbuttongap {
						height: 42px;
						flex: 0 0 42px;
					}
			STR;
		}
	?>
	
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<script>
	const PAGE_SEARCH_SPEED = 180;
	
	let urlParams = new URLSearchParams(window.location.search);
	let pageSearchSpeed = urlParams.has('pageSearchSpeed') ? parseInt(urlParams.get('pageSearchSpeed')) : PAGE_SEARCH_SPEED;
	let service = urlParams.has('service') ? urlParams.get('service') : "<?php echo DEFAULT_SERVICE; ?>";
	let recovery = urlParams.has('recovery') ? urlParams.get('recovery') : null;
	let page = urlParams.has('page') ? urlParams.get('page').toUpperCase() : "100";
	var currentPageNumber = page;
	var searchPageNumber = page;
	var searchDisplayedPageNumber = page;
	
	var pageListBuffer = null;
	var serviceListBuffer = null;
	var currentServiceIndex = 0;
	
	var bufferedSvg = null;
	var bufferedSvgPageNumber = null;
	
	var enablePageSearch = false;
	var pageSearchIndex = 0;
	
	var pageListTimeout = null;
	var serviceListTimeout = null;
	var refreshCurrentTeletextPageTimeout = null;
	var continuePageSearchTimeout = null;
	
	var resetIncompletePageSearchTimeout = null;
	
	var resize = 0;
	
	var xhrInitialiseTeletext;
	var xhrRefreshHeaderBeforePageSearch;
	var xhrGetPageList;
	var xhrGetServiceList;
	//var xhrLoadNewTeletextPage;
	var xhrLoadNewTeletextPageIntoBuffer;
	
	/*
	function loadNewTeletextPage() {
		if (xhrLoadNewTeletextPage) xhrLoadNewTeletextPage.abort();
		
		xhrLoadNewTeletextPage = $.ajax({
			type: "GET",
			url: "/teletext/viewer/tti_to_svg.php",
			data: { service: service, page: currentPageNumber, resize: resize },
			success: function (response) {
				clearTimeout(pageRotationTimeout);
				$("div#teletextscreen").html(response.rootElement.outerHTML);
			},
			error: function () {
				loadNewTeletextPage();
			},			
			timeout: 3000,
			dataType: "xml"
		});
	}
	*/
	
	//$(function () {
	//	functionPress(name);
	//});
	
	function loadNewTeletextPageIntoBuffer(renderOnLoad) {
		if (xhrLoadNewTeletextPageIntoBuffer) xhrLoadNewTeletextPageIntoBuffer.abort();
		
		xhrLoadNewTeletextPageIntoBuffer = $.ajax({
			type: "GET",
			url: "/teletext/viewer/tti_to_svg.php",
			data: { service: service, recovery: recovery, page: searchPageNumber, resize: resize, pageSearchSpeed: pageSearchSpeed },
			success: function (response) {
				clearTimeout(refreshCurrentTeletextPageTimeout);
				
				//clearTimeout(pageRotationTimeout);
				//$("div#teletextscreen").html(response.rootElement.outerHTML);
				bufferedSvg = response.rootElement.outerHTML;
				bufferedSvgPageNumber = searchPageNumber;
				if (renderOnLoad) renderNewTeletextPageFromBuffer();
				
				refreshCurrentTeletextPageTimeout = setTimeout(function() { loadNewTeletextPageIntoBuffer(true); }, 300000);
			},
			error: function (response) {
				if (response.status != "200" && response.status != "404" && response.status != "500") loadNewTeletextPageIntoBuffer(renderOnLoad);
			},			
			timeout: 3000,
			dataType: "xml"
		});
	}
	
	function renderNewTeletextPageFromBuffer() {			
		clearTimeout(pageRotationTimeout);
		$("div#teletextscreen").html(bufferedSvg);
	}	
	
	function beginPageSearch(bypass)
	{
		enablePageSearch = true;
		refreshHeaderBeforePageSearch(
			function () {
				$("div#teletextscreen").find("svg").find("text.header-row > tspan:not([fill]):first-child").css("fill", "lime");
				continuePageSearch();
				loadNewTeletextPageIntoBuffer(false);
			}
		);
	}
	
	function continuePageSearch(pageSearchIndex) 
	{
		clearTimeout(continuePageSearchTimeout);
		
		if (enablePageSearch && pageListBuffer[searchPageNumber.substring(0, 1)].length > 0)
		{			
			if (pageSearchIndex == null) pageSearchIndex = initialIndex(pageListBuffer[searchPageNumber.substring(0, 1)].length, pageSearchSpeed);
			currentPageNumber = pageListBuffer[searchPageNumber.substring(0, 1)][pageSearchIndex];
			
			//carousel page matches search page, but not ready and loaded in buffer
			if (currentPageNumber == searchPageNumber && bufferedSvgPageNumber != searchPageNumber)
			{
				pageSearchIndex++;
				if (pageSearchIndex >= pageListBuffer[searchPageNumber.substring(0, 1)].length) pageSearchIndex = 0;
				currentPageNumber = pageListBuffer[searchPageNumber.substring(0, 1)][pageSearchIndex];
			}
			
			//turns green, but shifts one character over
			//headerTemplateBuffer = headerTemplateBuffer.substring(0,6) + " \u001bB" + headerTemplateBuffer.substring(7);
			updateCurrentPageNumberInHeader();
			
			if (currentPageNumber == searchPageNumber && bufferedSvgPageNumber == searchPageNumber)
			{
				//loadNewTeletextPage();
				cancelPageSearch();
				$("div#teletextscreen").find("svg").find("text[class!='header-row']").css("visibility", "hidden");
				renderNewTeletextPageFromBuffer();
			}
			else
			{				
				if (pageSearchIndex < pageListBuffer[searchPageNumber.substring(0, 1)].length - 1)
				{
					pageSearchIndex++;
				}
				else
				{
					pageSearchIndex = 0;
				}
				
				
				//if (currentPageNumber == searchPageNumber && bufferedSvgPageNumber != searchPageNumber)
				//{
				//	continuePageSearch(pageSearchIndex);
				//}
				//else
				//{
					continuePageSearchTimeout = setTimeout(function() { continuePageSearch(pageSearchIndex); }, pageSearchSpeed);
				//}
			}
		}
	}
	
	function cancelPageSearch() 
	{
		enablePageSearch = false;
	}
	
	function initialIndex(totalLength, timePeriod)
	{
		let date = new Date();
		let time = date.getTime();
		
		let timeSpan = time / timePeriod;
		timeSpan = timeSpan.toFixed(0);
		
		let index = timeSpan % totalLength;
		return index;
	}
	
	function updateSearchPageNumberInHeader()
	{
		$("tspan#searchPageNumber").html("P" + searchDisplayedPageNumber + "&nbsp;&nbsp;&nbsp;&nbsp;");
	}
	
	function updateCurrentPageNumberInHeader()
	{
		$("tspan#currentPageNumber").html(currentPageNumber);
	}
	
	function resetIncompletePageSearch()
	{
		searchPageNumber = currentPageNumber;
		searchDisplayedPageNumber = currentPageNumber;
		$("tspan#searchPageNumber").html("P" + currentPageNumber + "&nbsp;&nbsp;&nbsp;&nbsp;");
	}
	
	function digitPress(digit)
	{
		clearTimeout(resetIncompletePageSearchTimeout);
		
		switch (searchPageNumber.length)
		{					
			case 3:
				if (digit >= 1 && digit <= 8)
				{
					cancelPageSearch();
					searchPageNumber = digit;
					searchDisplayedPageNumber = searchPageNumber + "--";
					resetIncompletePageSearchTimeout = setTimeout(function() { resetIncompletePageSearch(); }, 5000);
				}
				break;
			case 1:
				cancelPageSearch();
				searchPageNumber = searchPageNumber + "" + digit;
				searchDisplayedPageNumber = searchPageNumber + "-";
				resetIncompletePageSearchTimeout = setTimeout(function() { resetIncompletePageSearch(); }, 5000);
				break;
			case 2:
				cancelPageSearch();
				searchPageNumber = searchPageNumber + "" + digit;
				searchDisplayedPageNumber = searchPageNumber;
				beginPageSearch(false);
				break;
			default:
				break;
		}
		
		updateSearchPageNumberInHeader();
	}
	
	function fastextPress(buttonLabel)
	{
		let fastextSearchPageNumber = $("div#teletextscreen").find("svg").find("g.current-subpage").attr("data-fastext-" + buttonLabel);
		if (fastextSearchPageNumber.match(/[1-8]{1}[0-9A-F]{1}[0-9A-F]{1}/) && !fastextSearchPageNumber.match(/[1-8]{1}FF/))
		{
			cancelPageSearch();
			searchPageNumber = fastextSearchPageNumber;
			searchDisplayedPageNumber = searchPageNumber;
			updateSearchPageNumberInHeader();
			beginPageSearch(false);
		}
	}
	
	function functionPress(name)
	{
		let svg = $("div#teletextscreen").find("svg");
				
		switch (name)
		{
			case "hold":
				let searchPageNumberElement = svg.find("tspan#searchPageNumber");
				if (searchPageNumberElement.html() == "HOLD&nbsp;&nbsp;&nbsp;&nbsp;")
				{
					searchPageNumberElement.html("P" + searchDisplayedPageNumber + "&nbsp;&nbsp;&nbsp;&nbsp;");
					//if (svg.find("g").length > 1) pageRotation(svg.attr("id"), initialSubpageIndex(svg.find("g.subpage").length, svg.find("g.current-subpage").data("subpageCycleTime") * 1000), true);
					pageRotation(svg.attr("id"), initialSubpageIndex(svg.find("g.subpage").length, svg.find("g.current-subpage").data("subpageCycleTime") * 1000), true); //loop anyway, for world clock to run, even if only one page
					loadNewTeletextPageIntoBuffer(true);
					refreshCurrentTeletextPageTimeout = setTimeout(function() { loadNewTeletextPageIntoBuffer(true); }, 300000);
				}
				else
				{
					searchPageNumberElement.html("HOLD&nbsp;&nbsp;&nbsp;&nbsp;");
					clearTimeout(pageRotationTimeout);
					clearTimeout(refreshCurrentTeletextPageTimeout);
				}
				break;
			case "reveal":
				let concealedElements = svg.find("g").find("tspan.conceal");
				concealedElements.toggleClass("hidden");
				//concealedElements.css("visibility") == "visible" ? concealedElements.css("visibility", "hidden") : concealedElements.css("visibility", "visible");
				break;
			case "index":
				fastextPress("index");
				break;
			case "size":
				resize++;
				switch (resize)
				{
					case 3:
						resize = 0;
					case 0:
						svg.find("g.resizable-lines").removeClass("hidden");
						svg.find("g.resizable-lines").removeClass("resize");
						break;
					case 1:
						svg.find("g.resizable-lines.top-half").removeClass("hidden");
						svg.find("g.resizable-lines.bottom-half").removeClass("resize");
						svg.find("g.resizable-lines.top-half").addClass("resize");
						svg.find("g.resizable-lines.bottom-half").addClass("hidden");
						break;
					case 2:
						svg.find("g.resizable-lines.top-half").removeClass("resize");
						svg.find("g.resizable-lines.bottom-half").removeClass("hidden");
						svg.find("g.resizable-lines.top-half").addClass("hidden");
						svg.find("g.resizable-lines.bottom-half").addClass("resize");
						break;
					default:
						break;
				}
				break;
			case "previousService":
				if (recovery == null) {
					let previousServiceIndex = currentServiceIndex - 1;
					if (previousServiceIndex < 0) previousServiceIndex = serviceListBuffer.length - 1;
					
					urlParams.set("service", serviceListBuffer[previousServiceIndex]);
					window.location.href = "/teletext/viewer/?" + urlParams.toString().replace(/=&/g, "&").replace(/=$/, "");
				}
				break;
			case "nextService":
				if (recovery == null) {
					let nextServiceIndex = currentServiceIndex + 1;
					if (nextServiceIndex >= serviceListBuffer.length) nextServiceIndex = 0;
					
					let searchParams = new URLSearchParams(window.location.search);
					urlParams.set("service", serviceListBuffer[nextServiceIndex]);
					window.location.href = "/teletext/viewer/?" + urlParams.toString().replace(/=&/g, "&").replace(/=$/, "");
					break;
				}
			case "mute":
				if ($("audio")[0].paused) {
					if (!music_player.src) music_player.src = audioFiles[audioFilesIndex];
					$("audio")[0].muted = false;
					$("audio")[0].play();
					$("svg#volume-xmark").css("display", "none");
					$("svg#volume-high").css("display", "");
				}
				else {
					$("audio")[0].muted = !$("audio")[0].muted;
					$("svg#volume-xmark").css("display", $("audio")[0].muted ? "" : "none");
					$("svg#volume-high").css("display", $("audio")[0].muted ? "none" : "");
				}
				break;
			default:
				break;
		}
	}
	
	function initialiseTeletext()
	{		
		if (xhrInitialiseTeletext) xhrInitialiseTeletext.abort();
		
		xhrInitialiseTeletext = $.ajax({
			type: "GET",
			url: "/teletext/viewer/tti_to_svg.php",
			data: { service: service, recovery: recovery, page: page, headeronly: true },
			success: function (response) {
				$("div#teletextscreen").html(response.rootElement.outerHTML);
				getPageList(true);
			},
			error: function (response) {
				if (response.status != "404" && response.status != "500") initialiseTeletext();
			},
			timeout: 3000,
			dataType: "xml"
		});
	}
	
	function refreshHeaderBeforePageSearch(successFunction)
	{		
		if (xhrRefreshHeaderBeforePageSearch) xhrRefreshHeaderBeforePageSearch.abort();
		
		xhrRefreshHeaderBeforePageSearch = $.ajax({
			type: "GET",
			url: "/teletext/viewer/tti_to_svg.php",
			data: { service: service, recovery: recovery, page: searchPageNumber, headeronly: true },
			success: function (response) {
				let headerBackground = $(response.rootElement.outerHTML).find("text.header-row[data-layer='background']").html();
				let headerForeground = $(response.rootElement.outerHTML).find("text.header-row[data-layer='foreground']").html();
				$("text.header-row[data-layer='background']").html(headerBackground);
				$("text.header-row[data-layer='foreground']").html(headerForeground);
				updateSearchPageNumberInHeader();
				updateAllTimesDates();
				if (enablePageSearch) successFunction();
			},
			error: function (response) {
				if (response.status != "404" && response.status != "500") refreshHeaderBeforePageSearch(successFunction);
				if (response.status == "404") continuePageSearch();
			},
			timeout: 3000,
			dataType: "xml"
		});
	}
	
	function getPageList(startSearchOnLoad)
	{
		if (xhrGetPageList) xhrGetPageList.abort();
		
		xhrGetPageList = $.ajax({
			type: "GET",
			url: "/teletext/viewer/get_page_list.php",
			data: { service: service, recovery: recovery },
			success: function (response) {
				clearTimeout(pageListTimeout);
				pageListBuffer = response;
				if (startSearchOnLoad) beginPageSearch();
				pageListTimeout = setTimeout(function () { getPageList(false); }, 60000);
			},
			error: function () {
				getPageList(startSearchOnLoad);
			},
			timeout: 3000,
		});
	}
	
	function getServiceList()
	{
		if (xhrGetServiceList) xhrGetServiceList.abort();
		
		xhrGetServiceList = $.ajax({
			type: "GET",
			url: "/teletext/viewer/get_service_list.php",
			success: function (response) {
				clearTimeout(serviceListTimeout);
				serviceListBuffer = response;
				currentServiceIndex = serviceListBuffer.indexOf(service);
				serviceListTimeout = setTimeout(function () { getServiceList(); }, 300000);
			},
			error: function () {
				getServiceList();
			},
			timeout: 3000,
		});
	}
</script>

<div id="teletextcontainer">
	<?php
		if (isset($_GET['minitv'])) 
		{
			echo <<<STR
				<div id="teletextsurround">
					<div id="teletextscreen"></div>
				</div>
			STR;
		}
		else {
			echo <<<STR
				<div id="teletextscreen"></div>
			STR;
		}
	
		if (isset($_GET['fullscreen'])) 
		{
			echo <<<STR
			STR;
		}
		else
		{
			echo <<<STR
				<div id="teletextremote">
					<div id="remotebuttoncontainer">
						<div class="remotebutton">1</div>
						<div class="remotebutton">2</div>
						<div class="remotebutton">3</div>
						<div class="remotebutton">4</div>
						<div class="remotebutton">5</div>
						<div class="remotebutton">6</div>
						<div class="remotebutton">7</div>
						<div class="remotebutton">8</div>
						<div class="remotebutton">9</div>
						<div class="remotebuttongap"></div>
						<div class="remotebutton">0</div>
						<div class="remotebuttongap"></div>

						<div class="remoteverticalspacer"></div>

						<div class="fastextbutton" style="background-image: linear-gradient(red, brown);" data-name="red"></div>
						<div class="fastextbutton" style="background-image: linear-gradient(lightgreen, green);" data-name="green"></div>
						<div class="fastextbutton" style="background-image: linear-gradient(yellow, orange);" data-name="yellow"></div>
						<div class="fastextbutton" style="background-image: linear-gradient(blue, darkblue);" data-name="blue"></div>
						
						<div class="functionbutton" data-name="reveal">?</div>
						<div class="functionbutton" data-name="hold">
							<svg xmlns="http://www.w3.org/2000/svg" width="80%" height="80%" fill="currentColor" viewBox="0 0 21 21" style="pointer-events: none;">
								<g fill-rule="evenodd" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" transform="translate(3 1)">
								<path d="m4.5 4.5 3 3 3-3"/>
								<path d="m4.5 14.5 3-3 3 3"/>
								<path d="m.5 9.5h14"/>
								</g>
							</svg>
						</div>
						<div class="functionbutton" data-name="index">i</div>
						<div class="functionbutton" data-name="size">
							<svg xmlns="http://www.w3.org/2000/svg" width="80%" height="80%" fill="currentColor" viewBox="0 0 21 21" style="pointer-events: none;">
								<g fill-rule="evenodd" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" transform="translate(3 1)">
								<path d="m4.5 12.5 3 3 3-3"/>
								<path d="m4.5 6.5 3-3 3 3"/>
								<path d="m.5 9.5h14"/>
								</g>
							</svg>
						</div>

						<div class="remoteverticalspacer"></div>
					</div>
					
					<div id="remoteextrabuttoncontainer">
						<div class="remoteextrabutton" data-name="mute">
							<svg id="volume-xmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor" style="pointer-events: none;">
								<!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
								<path d="M301.1 34.8C312.6 40 320 51.4 320 64l0 384c0 12.6-7.4 24-18.9 29.2s-25 3.1-34.4-5.3L131.8 352 64 352c-35.3 0-64-28.7-64-64l0-64c0-35.3 28.7-64 64-64l67.8 0L266.7 40.1c9.4-8.4 22.9-10.4 34.4-5.3zM425 167l55 55 55-55c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-55 55 55 55c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-55-55-55 55c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l55-55-55-55c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0z"/>
							</svg>		
							<svg id="volume-high" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" fill="currentColor" style="pointer-events: none; display: none;">
								<!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
								<path d="M533.6 32.5C598.5 85.2 640 165.8 640 256s-41.5 170.7-106.4 223.5c-10.3 8.4-25.4 6.8-33.8-3.5s-6.8-25.4 3.5-33.8C557.5 398.2 592 331.2 592 256s-34.5-142.2-88.7-186.3c-10.3-8.4-11.8-23.5-3.5-33.8s23.5-11.8 33.8-3.5zM473.1 107c43.2 35.2 70.9 88.9 70.9 149s-27.7 113.8-70.9 149c-10.3 8.4-25.4 6.8-33.8-3.5s-6.8-25.4 3.5-33.8C475.3 341.3 496 301.1 496 256s-20.7-85.3-53.2-111.8c-10.3-8.4-11.8-23.5-3.5-33.8s23.5-11.8 33.8-3.5zm-60.5 74.5C434.1 199.1 448 225.9 448 256s-13.9 56.9-35.4 74.5c-10.3 8.4-25.4 6.8-33.8-3.5s-6.8-25.4 3.5-33.8C393.1 284.4 400 271 400 256s-6.9-28.4-17.7-37.3c-10.3-8.4-11.8-23.5-3.5-33.8s23.5-11.8 33.8-3.5zM301.1 34.8C312.6 40 320 51.4 320 64l0 384c0 12.6-7.4 24-18.9 29.2s-25 3.1-34.4-5.3L131.8 352 64 352c-35.3 0-64-28.7-64-64l0-64c0-35.3 28.7-64 64-64l67.8 0L266.7 40.1c9.4-8.4 22.9-10.4 34.4-5.3z"/>
							</svg>
						</div>
						<div id="exportCurrentPageAsImageButton" class="remoteextrabutton" title="Download a PNG of the current page" alt="Download a PNG of the current page">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="pointer-events: none;">
							  <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
							  <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
							</svg>
						</div>
						<div class="remoteextrabuttongap">
						</div>
					</div>
				</div>
			STR;
		}
	?>
</div>

<audio autoplay muted></audio>

<?php
	$audioFiles = glob("../music/*.mp3");
	$audioBaseFileNames = [];
	
	foreach ($audioFiles as $audioFile)
	{
		array_push($audioBaseFileNames, basename($audioFile));
	}
?>

<script>
// https://www.codeproject.com/Articles/5164334/Create-Music-Playlist-with-HTML5-and-JavaScript

		// Playlist array
		var audioFiles = <?php echo json_encode($audioFiles); ?>;
		var audioFilesIndex = 0;

		// Current index of the files array
		var audioFilesIndex = <?php echo rand(0, count($audioBaseFileNames) - 1); ?>;

		// Get the audio element
		var music_player = document.querySelector("audio");

		// function for moving to next audio file
		function next() {
			// Check for last audio file in the playlist
			if (audioFilesIndex === audioFiles.length - 1) {
				audioFilesIndex = 0;
			} else {
				audioFilesIndex++;
			}

			// Change the audio element source
			music_player.src = audioFiles[audioFilesIndex];
		}

		// Check if the player is selected
		if (music_player === null) {
			throw "Playlist Player does not exists ...";
		} else {
			// Start the player
			if (!$("audio")[0].muted) music_player.src = audioFiles[audioFilesIndex];

			// Listen for the music ended event, to play the next audio file
			// Note: loop must not be enabled on player, or 'ended' event won't fire
			music_player.addEventListener('ended', next, false);
		}
		
</script>

<script>		

	document.onkeydown = function(e)
	{
		if (!isNaN(e.key))
		{
			digitPress(e.key);
		}
		else
		{
			switch (e.key.toLowerCase())
			{
				case "r":
					fastextPress("red");
					break;
				case "g":
					fastextPress("green");
					break;
				case "y":
					fastextPress("yellow");
					break;
				case "b":
					fastextPress("blue");
					break;
				case "?":
				case "/":
					functionPress("reveal");
					break;
				case "h":
					functionPress("hold");
					break;
				case "i":
					fastextPress("index");
					break;
				case "s":
					functionPress("size");
					break;
				case "m":
					functionPress("mute");
					break;
				case "{":
				case "[":
					functionPress("previousService");
					break;
				case "}":
				case "]":
					functionPress("nextService");
					break;
				default:
					break;
			}
		}
	};
	
	function capitalizeFirstLetter(inputString) {
		return inputString.charAt(0).toUpperCase() + inputString.slice(1);
	}
	
	$("div#exportCurrentPageAsImageButton").on("click", function(e) {
		var canvas = document.createElement('canvas');
		var img = document.createElement('img');
		canvas.width = 1280;
		canvas.height = 1024;
		var ctx = canvas.getContext("2d");
		
		var xml = new XMLSerializer().serializeToString(document.getElementById("teletextscreen").getElementsByTagName("svg")[0]);
		
		// make it base64
		var svg64 = btoa(unescape(encodeURIComponent(xml)));
		var b64Start = 'data:image/svg+xml;base64,';

		// prepend a "header"
		var image64 = b64Start + svg64;

		// set it as the source of the img element
		img.onload = function() {
			const d = new Date();
			
			ctx.rect(0, 0, 1280, 1024);
			ctx.fillStyle = "black";
			ctx.fill();
			ctx.drawImage(img, 20, 12, 1228, 1008);
			
			//download PNG image
			var MIME_TYPE = "image/png";

			var imgURL = canvas.toDataURL(MIME_TYPE);

			var dlLink = document.createElement('a');
			dlLink.download = capitalizeFirstLetter(recovery ?? service) + " P" + currentPageNumber + " (" + d.getFullYear() + "-" + ("0"+(d.getMonth()+1)).slice(-2) + "-" + ("0" + d.getDate()).slice(-2) + " " + ("0" + d.getHours()).slice(-2) + "-" + ("0" + d.getMinutes()).slice(-2) + "-" + ("0" + d.getSeconds()).slice(-2) + ").png";
			dlLink.href = imgURL;
			dlLink.dataset.downloadurl = [MIME_TYPE, dlLink.download, dlLink.href].join(':');

			document.body.appendChild(dlLink);
			dlLink.click();
			document.body.removeChild(dlLink);
			
			//download alt text
			var MIME_TYPE = "text/plain";
			
			var currentAltText = $("div#teletextscreen").find("svg").find("g[style*='visibility: visible;']").find("title").html().split("\n");
			var outputAltText = "";
			
			let currentAltTextLength = currentAltText.length;
			for (let i = 0; i < currentAltTextLength; i++) {
				let altTextLine = currentAltText[i].trim();
				if (altTextLine.length > 0 || (i > 0 && currentAltText[i - 1].trim().length > 0)) outputAltText += altTextLine + "\n";
			}			
			
			var textURL = "data:text/plain;base64," + btoa(outputAltText);

			var dlLink2 = document.createElement('a');
			dlLink2.download = capitalizeFirstLetter(recovery ?? service) + " P" + currentPageNumber + " (" + d.getFullYear() + "-" + ("0"+(d.getMonth()+1)).slice(-2) + "-" + ("0" + d.getDate()).slice(-2) + " " + ("0" + d.getHours()).slice(-2) + "-" + ("0" + d.getMinutes()).slice(-2) + "-" + ("0" + d.getSeconds()).slice(-2) + ") (alt text).txt";
			dlLink2.href = textURL;
			dlLink2.dataset.downloadurl = [MIME_TYPE, dlLink.download, dlLink.href].join(':');

			document.body.appendChild(dlLink2);
			dlLink2.click();
			document.body.removeChild(dlLink2);
			
		}
		img.src = image64;
	});
	
	$(function() {
		getServiceList();
		initialiseTeletext();
			
		$("div.remotebutton:not([data-name])").on("click", function(e) {				
			let buttonLabel = e.target.innerHTML;
			digitPress(buttonLabel);
		});
		
		$("div.fastextbutton").on("click", function(e) {				
			fastextPress($(e.target).data("name"));
		});
		
		$("div.functionbutton, div.remotebutton[data-name], div.remoteextrabutton[data-name]").on("click", function(e) {
			functionPress($(e.target).data("name"));
		});
		
		if (<?php echo isset($_GET['unmute']) ? "true" : "false"; ?>) functionPress("mute");
	});
	
</script>