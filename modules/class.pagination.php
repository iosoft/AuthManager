<?php
/*
	@! AuthManager v3.0
	@@ User authentication and management web application
-----------------------------------------------------------------------------	
	** author: StitchApps
	** website: http://www.stitchapps.com
	** email: support@stitchapps.com
	** phone support: +91 9871084893
-----------------------------------------------------------------------------
	@@package: am_authmanager3.0
*/

/*
pagination class for the application. generates beautiful paginations. 
*/
class pagination {
	var $total_pages = -1;
	var $limit = null;
	var $target = "";
	var $page = 1;
	var $adjacents = 2;
	var $showCounter = false;
	var $className = "pagination";
	var $parameterName = "page";
	var $urlF = false;
	var $nextT = "Next";
	var $nextI = "&#187;";
	var $prevT = "Previous";
	var $prevI = "&#171;";
	var $calculate = false;

	function items($value) {
		$this->total_pages = (int) $value;
	}
	
	function limit($value) {
		$this->limit = (int) $value;
	}

	function target($value) {
		$this->target = $value;
	}

	function currentPage($value) {
		$this->page = (int) $value;
	}

	function adjacents($value) {
		$this->adjacents = (int) $value;
	}

	function showCounter($value = "") {
		$this->showCounter = ($value === true) ? true : false;
	}

	function changeClass($value = "") {
		$this->className = $value;
	}

	function nextLabel($value) {
		$this->nextT = $value;
	}
	
	function nextIcon($value) {
		$this->nextI = $value;
	}

	function prevLabel($value) {
		$this->prevT = $value;
	}

	function prevIcon($value) {
		$this->prevI = $value;
	}

	function parameterName($value = "") {
		$this->parameterName = $value;
	}

	function urlFriendly($value = "%") {
		if(preg_match("/^ *$/i", $value)) {
			$this->urlF = false;
			return false;
		}
		$this->urlF = $value;
	}

	function show() {
		if(!$this->calculate)
			if($this->calculate())
				echo "<div class=\"$this->className\"><ul>$this->pagination</ul></div>\n";
	}

	function getOutput() {
		if(!$this->calculate)
			if($this->calculate())
				return "<div class=\"$this->className\"><ul>$this->pagination</ul></div>\n";
	}

	function get_pagenum_link($id) {
		if(strpos($this->target, '?') === false)
			if($this->urlF)
				return str_replace($this->urlF, $id, $this->target);
			else
				return "$this->target?$this->parameterName=$id";
		else
			return "$this->target&$this->parameterName=$id";
	}

	function calculate() {
		$this->pagination = "";
		$this->calculate == true;
		$error = false;
			if($this->urlF and $this->urlF != '%' and strpos($this->target, $this->urlF) === false) {
				echo "Especificaste un wildcard para sustituir, pero no existe en el target<br />";
				$error = true;
			} elseif($this->urlF and $this->urlF == '%' and strpos($this->target, $this->urlF) === false) {
				echo "Es necesario especificar en el target el comodin % para sustituir el número de página<br />";
				$error = true;
			}

			if($this->total_pages < 0) {
				echo "It is necessary to specify the <strong>number of pages</strong> (\$class->items(1000))<br />";
				$error = true;
			}

			if($this->limit == null) {
				echo "It is necessary to specify the <strong>limit of items</strong> to show per page (\$class->limit(10))<br />";
				$error = true;
			}

			if($error)
				return false;

		$n = trim($this->nextT . ' ' . $this->nextI);
		$p = trim($this->prevI . ' ' . $this->prevT);

			if($this->page)
				$start = ($this->page - 1) * $this->limit;
			else
				$start = 0;

		$prev     = $this->page - 1;
		$next     = $this->page + 1;
		$lastpage = ceil($this->total_pages / $this->limit);
		$lpm1     = $lastpage - 1;

			if($lastpage > 1) {
				if($this->page) {
					if($this->page > 1)
						$this->pagination .= "<li class=\"prev\"><a href=\"" . $this->get_pagenum_link($prev) . "\">$p</a></li>";
					else
						$this->pagination .= "<li class=\"prev disabled\"><a href=\"#\">$p</a></li>";
				}
			if($lastpage < 7 + ($this->adjacents * 2)) {
				for($counter = 1; $counter <= $lastpage; $counter++) {
					if($counter == $this->page)
						$this->pagination .= "<li class=\"active\"><a href=\"#\">$counter</a></li>";
					else
						$this->pagination .= "<li><a href=\"" . $this->get_pagenum_link($counter) . "\">$counter</a></li>";
				}
			} elseif($lastpage > 5 + ($this->adjacents * 2)) {
				if($this->page < 1 + ($this->adjacents * 2)) {
					for($counter = 1; $counter < 4 + ($this->adjacents * 2); $counter++) {
						if($counter == $this->page)
							$this->pagination .= "<li class=\"active\"><a href=\"#\">$counter</a></li>";
						else
							$this->pagination .= "<li><a href=\"" . $this->get_pagenum_link($counter) . "\">$counter</a></li>";
					}
					$this->pagination .= "&hellip;";
					$this->pagination .= "<li><a href=\"" . $this->get_pagenum_link($lpm1) . "\">$lpm1</a></li>";
					$this->pagination .= "<li><a href=\"" . $this->get_pagenum_link($lastpage) . "\">$lastpage</a></li>";
				} elseif($lastpage - ($this->adjacents * 2) > $this->page && $this->page > ($this->adjacents * 2)) {
					$this->pagination .= "<li><a href=\"" . $this->get_pagenum_link(1) . "\">1</a></li>";
					$this->pagination .= "<li><a href=\"" . $this->get_pagenum_link(2) . "\">2</a></li>";
					$this->pagination .= "&hellip;";
					for($counter = $this->page - $this->adjacents; $counter <= $this->page + $this->adjacents; $counter++)
						if($counter == $this->page)
							$this->pagination .= "<li class=\"active\"><a href=\"#\">$counter</a></li>";
						else
							$this->pagination .= "<li><a href=\"" . $this->get_pagenum_link($counter) . "\">$counter</a></li>";
					$this->pagination .= "&hellip;";
					$this->pagination .= "<li><a href=\"" . $this->get_pagenum_link($lpm1) . "\">$lpm1</a></li>";
					$this->pagination .= "<li><a href=\"" . $this->get_pagenum_link($lastpage) . "\">$lastpage</a></li>";
				} else {
					$this->pagination .= "<li><a href=\"" . $this->get_pagenum_link(1) . "\">1</a></li>";
					$this->pagination .= "<li><a href=\"" . $this->get_pagenum_link(2) . "\">2</a></li>";
					$this->pagination .= "&hellip;";
					for($counter = $lastpage - (2 + ($this->adjacents * 2)); $counter <= $lastpage; $counter++)
						if($counter == $this->page)
							$this->pagination .= "<li class=\"active\"><a href=\"#\">$counter</a></li>";
						else
							$this->pagination .= "<li><a href=\"" . $this->get_pagenum_link($counter) . "\">$counter</a></li>";
				}
			}

			if($this->page) {
				if($this->page < $counter - 1) {
					$this->pagination .= "<li class=\"next\"><a href=\"" . $this->get_pagenum_link($next) . "\">$n</a></li>";
				} else {
					$this->pagination .= "<li class=\"next disabled\"><a href=\"#\">$n</a></li>";
				}

				if($this->showCounter)
					$this->pagination .= "<div class=\"pagination_data\">($this->total_pages Pages)</div>";
			}
		}
		return true;
	}
}
?>