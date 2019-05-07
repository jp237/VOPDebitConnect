<?php

class listView {
	var $sessName;
	var $rsList;
	var $counts;
	var $availCats;
	var $cat;
	var $filter;
	var $order;
	var $orderDir;
	var $startCount = 0;
	var $maxCount;
	var $fieldModes;
	var $maxCountArr;
	var $columns;
	var $scriptName = "VOPDebitConnect?";
	var $unsorted_list;
	var $customBtn;
	var $menuBtn;
	var $checkboxes;
	var $aktion;
	var $progressBar;
	var $sumBetrag;
	var $sumOffen;
	var $headLine;
	var $export;
	
	function enableAktion()
	{
		$this->aktion= true;
	}
	function enablecheckbox()
	{
		$this->checkboxes = true;
	}
	function __construct($aktionbtn = false,$menubtn = false,$checkbox = false,$progressBar = false)
	{
		if(($aktionbtn!=false))
		{
		$this->customBtn = $aktionbtn;
		$this->enableAktion();
		}
		else $this->customBtn = null;
		
		$this->progressBar = $progressBar;

		if($menubtn!=false)
		{
		$this->menuBtn = DC()->smarty->fetch($menubtn);
		}else
		$this->menuBtn = "";
		if($checkbox != false) $this->enablecheckbox();
	}
	
	function getCurrentOrder()
	{
			
	
		$this->maxCountArr = array(20,50,100,300,500,1000);
		
		if (isset(DC()->ListViewFilter[$this->sessName])) {
		
			$this->cat					= DC()->ListViewFilter[$this->sessName]['cat'];
			$this->order				= DC()->ListViewFilter[$this->sessName]['order'];
			$this->orderDir			= DC()->ListViewFilter[$this->sessName]['orderDir'];
			$this->filter				= DC()->ListViewFilter[$this->sessName]['filter'];
			$this->startCount		= DC()->ListViewFilter[$this->sessName]['startCount'];
			$this->maxCount			= DC()->ListViewFilter[$this->sessName]['maxCount'];
			$this->fieldModes		= DC()->ListViewFilter[$this->sessName]['fieldModes'];
		} else {
			$this->cat					= "none";
			$this->order				= "id";
			$this->orderDir			= "DESC";
			if ($defaultFilter) $this->filter = $defaultFilter;
				else $this->filter = array();
			$this->startCount		= 0;
			$this->maxCount			= $this->maxCountArr[0];
			$this->fieldModes['none'] = "";
		}
		$this->getFormData();
		
	}
	
	function createUnsortedList($aDataType,$maxCounts) {
		$this->rsList = $aDataType;
		$this->counts = $maxCounts;

	}
		
	function createList() {
	

	$this->sumBetrag = 0.00;
	$this->sumOffen = 0.00;
			
				$output = "";
				if ($this->rsList) {
			
			
			foreach ($this->rsList as $singleRow) {
					foreach($singleRow as $key => $val)
					{
						$cssClass = isset($singleRow['cssclass']) ? "class='".$singleRow['cssclass']."font'" : "";
						$output .= "<tr ".$cssClass." id='".$val."'>";
						break;
					}
			$exportRow = array();	
			
			
				if($this->checkboxes){
					foreach($singleRow as $key => $val)
					{// NUR ERSTEN INDEX AUS DEM ARRAY NEHMEN
					
						if(isset($singleRow["mahnstop"]) && $singleRow["mahnstop"]>0)
						{
						$output.="<td align=\"left\"><img src='/engine/Shopware/Plugins/Community/Backend/VOPDebitConnect/Views/backend/_resources/img/cancel.png'</td>";
						break;
						}
						else
						$output.="<td align=\"left\"><input type='checkBox' name='cbx[".$val."]' value='".$val."'></td>";
						break;
					}
				}
				
				if($this->progressBar)
				{
					$output.="<td>".$this->createProgressbar($singleRow["percentvalue"])."</td>";
				}
			
				if(isset($this->columns["sumGesamt"]))
				{
					$this->sumBetrag = $this->sumBetrag+$singleRow[$this->columns["sumGesamt"][1]];
				}
				if(isset($this->columns["sumOffen"]))
				{
					$this->sumOffen = $this->sumOffen+$singleRow[$this->columns["sumOffen"][1]];
				}
					foreach($this->columns as $key => $value)
				{ 
				
					$col = explode(".",$value[1]);
					if($value[0]==true)
					{
						
						if(count($col)>1){
						$exportRow[] = $singleRow[$col[1]] ? $singleRow[$col[1]] : "";
						$output.="<td align=\"left\">".$singleRow[$col[1]]."</td>";
						}
						else{
						$exportRow[] = $singleRow[$col[0]] ? $singleRow[$col[0]] : "";
						$output.="<td align=\"left\">".$singleRow[$col[0]]."</td>";
						}
					}
					
				}
				
				if($this->aktion) $output.="<td  align=\"left\">".$this->createCustomBtn($singleRow["id"])."</td>";

				$output.="</tr>";
				$this->export->csv[] = $exportRow;
			}
		}
		
		if($this->counts<1)
		{
			$output.="<tr><td colspan='20'><div align='center'>Keine Ergebnisse in ihrer Suche</div></td></tr>";
		}
		if($this->sumBetrag>0 || $this->sumOffen>0)
		{
			
			if($this->sumBetrag>0) $summe.=" Betrag : ".number_format($this->sumBetrag,2,",",".")."   ";
			if($this->sumOffen>0) $summe.=" Offen : ".number_format($this->sumOffen,2,",",".");
			$output .= "<tr><td class='sumRow'  align='center' colspan='".count($this->columns)."'>$summe</td></tr>";
		}
		DC()->Export = $this->export;
		$exportButton = "";
		if(count(DC()->Export->csv)>1){
			$exportButton = "<a href='VOPDebitConnect?export' target='_new' class='btn btn-primary'>CSV-Export</a>";
		}
		$output.="</table>";
		
		$output.="<div style='padding-top:15px;text-align:right'>$exportButton $this->menuBtn</div></div>";
		$this->rsList = null;
		return $output;
	}
	
	
	function createProgressbar($width)
	{
		
		if($width<0)
		{
			return "<div>In K&uuml;rze</div>";
		}
		$width = 100-$width;
		return "<div class='progresswrapper'><div class='progresscounter' style='width:".$width."%'></div></div>";
	}
	
	function createCustomBtn($id)
	{
	
			$btn = $this->customBtn;
		$a = "<a ";
		if(isset($btn["cssclass"])) $a.= " class='".$btn["cssclass"]."' ";
		if(isset($btn["cssid"])) $a.= " id='".$btn["cssid"]."' ";
		if(isset($btn["href"])) $a.= " onclick='showLoader();' href='".$btn["href"]."$id' ";
		else if(isset($btn["data-fancy-href"])) $a.= "data-fancybox-href='".$btn["data-fancy-href"]."$id' ";
		$a.= ">".$btn["text"]."</a>";
		return $a;
	}
	
	function createHeader($db_field,$headertext,$fieldLength=0,$fieldData=False,$fieldModeArr=False,$noBR=False,$db_field_sort=False,$showHeader=True) {
		$scriptName = $this->scriptName;
		
		global $scriptName;
		if ($db_field_sort) $sortField = $db_field_sort;
			else $sortField = $db_field;
		if ($this->order == $sortField) {
			$linkColor = "style='color:red;'";
			$linkTitle = "Sortierung �ndern";
			if ($this->orderDir == "DESC") {
				$linkDir = "&nbsp;<img src='/engine/Shopware/Plugins/Community/Backend/VOPDebitConnect/Views/backend/_resources/img/absteigend.png' width='7' height='7' alt='absteigend'>";
			} else {
				$linkDir = "&nbsp;<img src='/engine/Shopware/Plugins/Community/Backend/VOPDebitConnect/Views/backend/_resources/img/aufsteigend.png' width='7' height='7' alt='aufsteigend'>";
			}
		} else {
			$linkColor = "";
			$linkTitle = "Reihenfolge &auml;ndern";
			$linkDir = "";
		}
		if ($showHeader) $html = "<a href='".$this->scriptName."&cmd=changeOrder&changeto=".$sortField."' title='".$linkTitle."'".$linkColor.">".$headertext."</a>".$linkDir;
			else $html = "";
		if ($fieldModeArr) {
			if ($html != "") $html .= "<br>";
			$standardElement = array("id" => "" , "description" => "");
			array_unshift($fieldModeArr,$standardElement);
			$html .= "<select class='form-control input-sm' name='setFieldMode[".$db_field."]' size='1' style='max-width:150px;' onchange='showLoader();document.fmResults.submit();'>";
			foreach ($fieldModeArr as $arrayRow) {
		
				$showbez = $arrayRow['description'];
				$value = $showbez;
				if (isset($this->fieldModes[$db_field]) AND $this->fieldModes[$db_field] == $value) $selected = " selected";
					else $selected = "";
				$html .= "<option value='".$showbez."'".$selected.">".$showbez."</option>";
			} 
			//$html .= "</select>";
		}
	
		
		if ($fieldLength AND !$fieldData) {
		
			if (isset($this->filter[$db_field])) $fieldValue = $this->filter[$db_field];
				else $fieldValue = "";
			if (!$noBR) $html .= "<br>";
			$html .= "<input type='text' class='form-control' name='setFilter[".$db_field."]' style='max-width:150px;width:".$fieldLength."px;' value='".$fieldValue."' maxlength='30'>";
		}
		if (!$fieldLength AND $fieldData) {
			if (!$noBR) $html .= "<br>";
			$html .= "<select name='setFilter[".$db_field."]' size='1' class='form-control' onchange='showLoader();document.fmResults.submit();'><option value=''>&nbsp;</option>";
			foreach ($fieldData as $value => $showbez) {
				if (isset($this->filter[$db_field]) AND $this->filter[$db_field] == $value) $selected = " selected";
					else $selected = "";
				$html .= "<option value='".$value."'".$selected.">".$showbez."</option>";
			}
			$html .= "</select>";
		}
		return $html;
	}
	
		

	function createMaxCountSelector() {
		
		$msg = "Suchergebnisse: ".$this->counts;
		$msg .= "&nbsp; Maximal : <select size='1' name='setMaxCount' onchange='showLoader();document.fmResults.submit();'>";
		foreach ($this->maxCountArr as $sCount) {
			if ($this->maxCount == $sCount) $sel = " selected";
				else $sel = "";
			$msg .= "<option value='".$sCount."'".$sel.">".$sCount."</option>";
		}
		if ($this->maxCount == 9999) $sel = " selected";
			else $sel = "";
		// $msg .= "<option value='9999'".$sel.">alle</option>";
		$msg .= "</select>";
		return $msg;
	}
 
 
 	function createListViewHeader($scriptName = "VOPDebitConnect",$headline = "DEFINE TITLE")
	{
		$this->export = new stdClass();
		$this->export->headLine = $this->headLine;
		$this->headLine = $headline;
		$this->export->csv = array();
		$header ="<div class='box-group list'><form name=\"fmResults\" method=\"post\" action=\"$scriptName\">
					<input type=\"hidden\" name=\"cmd\" value=\"configListView\">
					<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
				<tr valign=\"top\">
				<td><h4 style='color:#9e1616'>$headline</h4>
				<td align=\"right\" valign=\"bottom\" nowrap>$checkboxcontrol Filter&nbsp;<input class='button'  type=\"submit\" value=\"setzen\">&nbsp;<input class='button' type=\"button\" style='padding-right:15px' value=\"l&ouml;schen\" onClick=\"showLoader();location.href='".$this->scriptName."&cmd=resetFilter';\">".$this->createMaxCountSelector().$this->createPageSelector()."</td>
				
				</tr>
				<tr><td  colspan='2'></td></tr>
				</table>";
				$header.="<table class=\"auftragtable\" border=\"0\" width='100%' align=\"left\" cellpadding=\"1\" cellspacing=\"0\">";
				$header.="<thead><tr class='headingTr'>";
				if($this->checkboxes) $header.="<td><input class='checkall' type='checkbox' name='selectAll'></td>";
				if($this->progressBar) $header.="<td>Fortschritt</td>";
				$exportHeaderRow = array();
				foreach($this->columns as $key => $value)
				{
					if($value[0]==true)
					{
						$exportHeaderRow[] = $value[2];
						$arrayFilter = is_array($value["arrayFilter"]) ? $value["arrayFilter"] : null;
						
						$suche = $value[3] ? 100:0;
						$header.="<td>".$this->createHeader($value[1],$value[2],$suche,"",$arrayFilter)."</td>";
					}
				}
				$this->export->csv[] = $exportHeaderRow;
				if($this->aktion) $header.="<td></td>";
				$header.="</tr></thead>";
				
			
				
					
				return $header;
	}
	function createPageSelector() {
		
		
		global $scriptName;
		$page = intval($this->startCount / $this->maxCount) + 1;
		$msg = "Seite ";
		$msg .= "<select size='1' name='setStartCount' onchange='showLoader();document.fmResults.submit();'>";
		for ($i=0; $i < $this->counts; $i += $this->maxCount) {
			if ($i >= $this->startCount AND $i < ($this->startCount + $this->maxCount)) $sel = " selected";
				else $sel = "";
			$msg .= "<option value='".$i."'".$sel.">".(intval($i / $this->maxCount) + 1)."</option>";
		}
		$msg .= "</select>";
		$msg .= " von ".ceil($this->counts / $this->maxCount)."";
		if ($this->startCount < $this->maxCount) $dis = " disabled";
			else $dis = "";
		$msg .= "<input type='button' class='button'  value='|<' style='width:30px;' onclick='showLoader();document.location.href=\"".$this->scriptName."&cmd=changeStartCount&changeto=0\";'".$dis.">";
		if ($this->startCount - $this->maxCount > $this->maxCount) $changeto = $this->startCount - $this->maxCount;
			else $changeto = 0;
		if ($this->startCount < $this->maxCount) $dis = " disabled";
			else $dis = "";
		$msg .= "<input type='button' class='button'  value='<' style='width:30px;' onclick='showLoader();document.location.href=\"".$this->scriptName."&cmd=changeStartCount&changeto=".$changeto."\";'".$dis.">";
		$changeto = $this->startCount + $this->maxCount;
		if ($this->startCount + $this->maxCount > $this->counts) $dis = " disabled";
			else $dis = "";
		$msg .= "<input type='button' class='button'  value='>' style='width:30px;' onclick='showLoader();document.location.href=\"".$this->scriptName."&cmd=changeStartCount&changeto=".$changeto."\";'".$dis.">";
		$changeto = $this->counts - $this->maxCount + 1;
		if ($this->startCount > $this->counts - $this->maxCount) $dis = " disabled";
			else $dis = "";
		$msg .= "<input type='button' class='button'  value='>|' style='width:30px;' onclick='showLoader();document.location.href=\"".$this->scriptName."&cmd=changeStartCount&changeto=".$changeto."\";'".$dis.">";
		return $msg;
	}

	function setStartCount($newStartCount) {
		$this->startCount = $newStartCount;
		$this->writeSession();
	}

	function setMaxCount($newMaxCount) {
		$this->maxCount = $newMaxCount;
		$this->writeSession();
	}

	function setFieldMode($fieldModes) {
		$this->fieldModes = $fieldModes;
		$this->writeSession();
	}

	function setCat($cat) {
		$this->cat = $cat;
		$this->writeSession();
	}

	function changeCat($whatcat) {
		$this->cat = $whatcat;
		$this->writeSession();
	}
	
	
	function changeOrder($whatorder) {
		if ($this->order == $whatorder) {
			if ($this->orderDir == "ASC") {
				$this->orderDir = "DESC";
			} else {
				$this->orderDir = "ASC";
			}
		} else {
			$this->order = $whatorder;
		}
		$this->writeSession();
	}

	function setFilter($fmFilter) {
		if(count($fmFilter)>1){
		
		foreach (@$fmFilter as $field => $value) {
			if ($value) $this->filter[$field] = $value;
				else unset($this->filter[$field]);
		}
		}
		$this->writeSession();
	}

	function addFilter($field,$value) {
		$this->filter[$field] = trim($value);
		$this->writeSession();
	}

	function delFilter($field) {
		unset($this->filter[$field]);
		$this->writeSession();
	}

	function resetFilter() {
		$this->filter = array();
		$this->writeSession();
	}

	function writeSession() {
		
		DC()->ListViewFilter[$this->sessName]['cat'] = $this->cat;
		DC()->ListViewFilter[$this->sessName]['filter'] = $this->filter;
		DC()->ListViewFilter[$this->sessName]['order'] = $this->order;
		DC()->ListViewFilter[$this->sessName]['orderDir'] = $this->orderDir;
		DC()->ListViewFilter[$this->sessName]['startCount'] = $this->startCount;
		DC()->ListViewFilter[$this->sessName]['maxCount'] = $this->maxCount;
		DC()->ListViewFilter[$this->sessName]['fieldModes'] = $this->fieldModes;
	}

	function catSelected($whatcat) {
		if ($whatcat == $this->cat) {
			return " selected";
		} else {
			return;
		}
	}

	
	function getFormData() {
		global $cmd,$cb,$changeto;
		$cmd				= $this->getVar('cmd');
		$cb					= $this->getVar('cb');
		$changeto		= $this->getVar('changeto');
		$filter			= $this->getVar('setFilter');
		$fieldMode	= $this->getVar('setFieldMode');
		$startCount	= $this->getVar('setStartCount');
		$maxCount		= $this->getVar('setMaxCount');
		$cat				= $this->getVar('setCat');
		if ($cmd == "configListView") {
			$this->setCat($cat);
			$oldFilter = $this->filter;
			$this->setFilter($filter);
			if ($this->filter != $oldFilter) $startCount = 0;			// wenn sich der Filter ge�ndert hat, also die Anzahl der Suchergebnisse, dann setze Startseite auf 1
			$this->setStartCount($startCount);
			$this->setMaxCount($maxCount);
			$this->setFieldMode($fieldMode);
		}
		if ($cmd == "setFilterDefault") {
			$this->resetFilter();
			$this->setFilter($filter);
		}
		if ($cmd == "changeStartCount") $this->setStartCount($changeto);
		if ($cmd == "changeOrder") $this->changeOrder($changeto);
		if ($cmd == "changeCat") $this->changeCat($changeto);
		if ($cmd == "resetFilter") $this->resetFilter();
		if ($cmd == "cancelNachbearbeitung") $this->cancelNachbearbeitung($changeto);
	}

	function getVar($varname,$varlen=30) {
		$returnvalue = DC()->get($varname);
		if (is_array($returnvalue)) return $returnvalue;
			else return substr($returnvalue,0,$varlen);
	}

}

?>