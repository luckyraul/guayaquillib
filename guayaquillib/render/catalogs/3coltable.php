<?php

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'template.php';

class GuayaquilCatalogsList extends GuayaquilTemplate
{
	var $iconsFolder = 'images/';
	var $columns = array('icon', 'name', 'version');
	var $catalogs = NULL;

	function __construct(IGuayaquilExtender $extender)
	{
		parent::__construct($extender);

		$this->iconsFolder = $this->Convert2uri(dirname(__FILE__).DIRECTORY_SEPARATOR.$this->iconsFolder).'/';
	}

	function Draw($catalogs)
	{
		$html = '<table border="0" width="100%"><tr><td>';

		$total = count($catalogs->row);
		$divide1 = ceil($total / 3);
		$divide2 = $divide1 * 2;
		$index = 1;

		foreach ($catalogs->row as $row) 
		{ 
			$link = $this->FormatLink('catalog', $row, (string)$row->code);

			if ($index == 1 || $index == $divide1+1 || $index == $divide2+1)
			{
				$html .= '<table class="guayaquil_tablecatalog" border="0">';
				$html .= $this->DrawHeader();
			}
			$html .= $this->DrawRow($row, $link);

			if ($index == $divide1 || $index == $divide2)
				$html .= '</table></td><td valign=top>';
			if ($index == $total)
				$html .= '</table>';

			$index = $index + 1;
		} 

		$html .= '</td></tr></table>';
		$html .= '<div style="visibility: visible; display: block; height: 20px; text-align: right;"><a href="http://dev.laximo.ru" rel="follow" style="visibility: visible; display: inline; font-size: 10px; font-weight: normal; text-decoration: none;">guayaquil</a></div>';

		return $html;
	}

	function DrawHeader()
	{
		$html .= '<tr>';
		
		foreach ($this->columns as $column)
			$html .= $this->DrawHeaderCell(strtolower($column));

		$html .= '</tr>';
		return $html;
	}
	
	function DrawHeaderCell($column)
	{
		return '<th>'.$this->DrawHeaderCellValue($column).'</th>';
	}

	function DrawHeaderCellValue($column)
	{
		switch ($column)
		{
			case 'icon':
				return '&nbsp;';

			case 'name':
				return $this->GetLocalizedString('Catalog title');

			case 'version':
				return $this->GetLocalizedString('Catalog date');
		}

		return '';
	}

	function DrawRow($catalog, $link)
	{
		$html = '<tr onmouseout="this.className=\'\';" onmouseover="this.className=\'over\';" onclick="window.location=\''.$link.'\'">';
		foreach ($this->columns as $column)
			$html .= $this->DrawCell($catalog, strtolower($column), $link);

		$html .= '</tr>';
		return $html;
	}

	function DrawCell($catalog, $column, $link)
	{
		return '<td valign="center">'.$this->DrawCellValue($catalog, $column, $link).'</td>';
	}

	function DrawCellValue($catalog, $column, $link)
	{
		switch ($column)
		{
			case 'icon':
				return '<a class="guayaquil_tablecatalog" href="'.$link.'"><img border="0" width="40" height="40" src="'.$this->iconsFolder.strtolower($catalog['icon']).'"></a>';

			case 'name':
				return '<a class="guayaquil_tablecatalog" href="'.$link.'">'.$catalog['name'].'</a>';

			case 'version':
				return '<a class="guayaquil_tablecatalog" href="'.$link.'">'.$catalog['version'].'</a>';
		}

		return '';
	}
}

?>