<?php

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'template.php';
require_once 'image.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'details'.DIRECTORY_SEPARATOR.'detailslist.php';

class GuayaquilUnit extends GuayaquilTemplate
{
	var $categories = NULL;
	var $catalog = NULL;
	var $ssd = NULL;

	var $unitimagerenderer = NULL;
	var $detaillistrenderer = NULL;

	var $closedimage = '../details/images/closed.gif';
	var $cartimage = '../details/images/cart.gif';
	var $detailinfoimage = '../details/images/info.gif';
	var $mouse_wheel = '../images/mouse_wheel.png';
	var $mouse = '../images/mouse.png';
	var $lmb = '../images/lmb.png';
	var $move = '../images/move.png';
	var $arrow = '../images/pointer.png';

	var $containerwidth = 960;
	var $containerheight = 600;
	var $drawlegend = 1;

    var $drawtoolbar = true;

	function __construct(IGuayaquilExtender $extender)
	{
		parent::__construct($extender);

		$this->unitimagerenderer = $this->CreateUnitImageRenderer();
		$this->detaillistrenderer = $this->CrateDetailListRenderer();

		$this->closedimage = $this->Convert2uri(dirname(__FILE__).DIRECTORY_SEPARATOR.$this->closedimage);
		$this->cartimage = $this->Convert2uri(dirname(__FILE__).DIRECTORY_SEPARATOR.$this->cartimage);
		$this->detailinfoimage = $this->Convert2uri(dirname(__FILE__).DIRECTORY_SEPARATOR.$this->detailinfoimage);
		$this->mouse_wheel = $this->Convert2uri(dirname(__FILE__).DIRECTORY_SEPARATOR.$this->mouse_wheel);
		$this->mouse = $this->Convert2uri(dirname(__FILE__).DIRECTORY_SEPARATOR.$this->mouse);
		$this->lmb = $this->Convert2uri(dirname(__FILE__).DIRECTORY_SEPARATOR.$this->lmb);
		$this->move = $this->Convert2uri(dirname(__FILE__).DIRECTORY_SEPARATOR.$this->move);
		$this->arrow = $this->Convert2uri(dirname(__FILE__).DIRECTORY_SEPARATOR.$this->arrow);
	}

	function CreateUnitImageRenderer()
	{
		return new GuayaquilUnitImage($this->extender);
	}

	function CrateDetailListRenderer()
	{
		return new GuayaquilDetailsList($this->extender);
	}

	function Draw($catalog, $unit, $imagemap, $details, $replacements, $cataloginfo, $prices = array(), $availability = array())
	{
		$this->catalog = $catalog;
		$this->ConfigureUnitImageRenderer($this->unitimagerenderer);
		$this->ConfigureDetailListRenderer($this->detaillistrenderer);

        if (CommonExtender::isFeatureSupported($cataloginfo, 'quickgroups'))
            GuayaquilToolbar::AddButton($this->GetLocalizedString('QuickGroupsLink'), $this->FormatLink('quickgroup', null, $this->catalog));
        $unit['note']='';
        foreach ($unit->attribute as $attr)
            $unit['note'].='<b>'.(string) ($attr->attributes()->name) .'</b>: ' .(string) $attr->attributes()->value.'<br/>';
        $note = $this->GetNote($unit['note']);
        if ($note)
            echo '<p class="guayaquil_unit_note">'.$note.'</p>';

        $html = $this->drawtoolbar ? GuayaquilToolbar::Draw() : '';

        $html .= $this->DrawContainer($catalog, $unit, $imagemap, $details, $replacements, $prices, $availability);

		if ($this->drawlegend)
			$html .= $this->DrawLegend();

		$html .= '<div style="visibility: visible; display: block; height: 20px; text-align: right;"><a href="http://dev.laximo.ru" rel="follow" style="visibility: visible; display: inline; font-size: 10px; font-weight: normal; text-decoration: none;">guayaquil</a></div>';

		return $html;
	}

    function GetNote($note)
    {
        return str_replace("\n", '<br>', (string)$note);
    }

	function ConfigureUnitImageRenderer($unitimagerenderer)
	{
		$unitimagerenderer->containerwidth = $this->containerwidth;
		$unitimagerenderer->containerheight = $this->containerheight;
	}

	function ConfigureDetailListRenderer($detaillistrenderer)
	{

	}

	function DrawContainer($catalog, $unit, $imagemap, $details, $replacements, $prices, $availability)
	{
		$html = '<div id="g_container" style="vertical-align:top;height:'.($this->containerheight + 2).'px;width:100%; overflow:hidden;">';

		$html .= $this->DrawUnitImage($catalog, $unit, $imagemap);
		$html .= $this->DrawDetailList($catalog, $details, $replacements, $prices, $availability);

		$html .= '</div>';

		return $html;
	}

	function DrawUnitImage($catalog, $unit, $imagemap)
	{
		$html = '<div style="width:49%; height:100%;" class="inline_block">';

		$html .= $this->unitimagerenderer->Draw($catalog, $unit, $imagemap);
		
		$html .= '</div>';

		return $html;
	}

	function DrawDetailList($catalog, $details, $replacements, $prices, $availability)
	{
		$html = '<div id="viewtable" style="overflow:auto; width:49%; height:100%;" class="inline_block">';

		$html .= $this->detaillistrenderer->Draw($catalog, $details, $replacements, $prices, $availability);

        $html .= '</div>';

		return $html;
	}

	function DrawLegend()
	{
        $html  = '<table width="100%" border=0 style="margin-top:5px">';
        $html .= '<tr>';
        $html .= '<th align=center colspan=4>'.$this->GetLocalizedString('UNIT_LEGEND').'</th>';
        $html .= '</tr><tr>';
        $html .= '<td align=left><img src="'.$this->mouse_wheel.'"></td>';
        $html .= '<td> - '.$this->GetLocalizedString('UNIT_LEGEND_IMAGE_RESIZING').'</td>';
        $html .= '<td align=left><img src="'.$this->lmb.'"> <img src="'.$this->move.'"></td>';
        $html .= '<td> - '.$this->GetLocalizedString('UNIT_LEGEND_MOUSE_SCROLL_IMAGE').'</td>';
        $html .= '</tr><tr>';
        $html .= '<td align=center><img src="'.$this->mouse.'"> <img src="'.$this->arrow.'"></td>';
        $html .= '<td> - '.$this->GetLocalizedString('UNIT_LEGEND_HIGHLIGHT_PARTS').'</td>';
        $html .= '<td align=left><img src="'.$this->lmb.'">   <img src="'.$this->detailinfoimage.'"></td>';
        $html .= '<td> - '.$this->GetLocalizedString('UNIT_LEGEND_SHOW_HIND').'</td>';
        $html .= '</tr></table>';
		return $html;
	}

    function DrawQuickGroupsLink()
    {
        $link = $this->FormatLink('quickgroup', null, $this->catalog);
        return '<div class="gQuickGroupsLink"><a href="'.$link.'">'.$this->GetLocalizedString('QuickGroupsLink').'</a></div>';
    }
}
