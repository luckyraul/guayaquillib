<?php

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'template.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'details'.DIRECTORY_SEPARATOR.'detailslist.php';

?>
<script type="text/javascript">
jQuery(document).ready(function(){
    jQuery('.guayaquil_zoom').colorbox({
        href: function () {
                var url = jQuery(this).attr('full');
                return url;
            },
        photo:true,
        rel: "img_group",
        opacity: 0.3,
        title : function () {
            var title = jQuery(this).attr('title');
            var url = jQuery(this).attr('link');
            return '<a href="' + url + '">' + title + '</a>';
        },
        current: 'Рис. {current} из {total}',
        maxWidth : '98%',
        maxHeight : '98%'
        }
    )
});

</script>
<?php

class GuayaquilQuickDetailsList extends GuayaquilTemplate
{
	var $groups = NULL;
	var $vehicleid = NULL;
	var $ssd = NULL;
    var $catalog;

    var $currentcategory;
    var $currentunit;
    var $currentdetail;

    var $foundunit = false;

    var $closedimage = '../details/images/closed.gif';
    var $cartimage = '../details/images/cart.gif';
    var $detailinfoimage = '../details/images/info.gif';
    var $zoom_image = NULL;
    var $size = 175;

    var $drawtoolbar = true;

    function __construct(IGuayaquilExtender $extender)
    {
        parent::__construct($extender);

        $this->detaillistrenderer = $this->CrateDetailListRenderer();
        $this->detaillistrenderer->group_by_filter = 1;

        $this->closedimage = $this->Convert2uri(dirname(__FILE__).DIRECTORY_SEPARATOR.$this->closedimage);
        $this->cartimage = $this->Convert2uri(dirname(__FILE__).DIRECTORY_SEPARATOR.$this->cartimage);
        $this->detailinfoimage = $this->Convert2uri(dirname(__FILE__).DIRECTORY_SEPARATOR.$this->detailinfoimage);
        $this->zoom_image = $this->Convert2uri(dirname(__FILE__).'/../images/zoom.png');

        $this->AppendJavaScript(dirname(__FILE__).'/../jquery.colorbox.js');
        $this->AppendCSS(dirname(__FILE__).'/../colorbox.css');
    }

    protected function CrateDetailListRenderer()
    {
        return new GuayaquilDetailsList($this->extender);
    }

	function Draw($details, $catalog, $vehicle_id, $ssd)
	{
        $this->catalog = $catalog;
        $this->vehicleid = $vehicle_id;
        $this->ssd = $ssd;

        GuayaquilToolbar::AddButton($this->GetLocalizedString('VehicleLink'), $this->FormatLink('vehicle', null, $this->catalog));

        $html = $this->drawtoolbar ? GuayaquilToolbar::Draw() : '';

        foreach ($details->Category as $category)
            $html .= $this->DrawCategory($category);

        if (!$this->foundunit)
            $html .= $this->DrawEmptySet();

		$html .= '<div style="visibility: visible; display: block; height: 20px; text-align: right;"><a href="http://dev.laximo.ru" rel="follow" style="visibility: visible; display: inline; font-size: 10px; font-weight: normal; text-decoration: none;">guayaquil</a></div>';

		return $html;
	}

    protected function DrawCategory($category)
    {
        $this->currentcategory = $category;

        $html = '<div class="gdCategory">'.
            $this->DrawCategoryContent($category);

        foreach ($category->Unit as $unit)
            $html .= $this->DrawUnit($unit);

        $html .= '</div>';

        return $html;
    }

    protected function DrawCategoryContent($category)
    {
        $link = $this->FormatLink('category', $category, $this->catalog);
        return '<h3><a href="'.$link.'">'.$category['name'].'</a></h3>';
    }

    protected function DrawUnit($unit)
    {
        $this->currentunit = $unit;
        $this->foundunit = true;

        return '<table class="gdUnit">
            <tr>
                <td class="gdImageCol" width="'.($this->size + 4).'" align=center valign=top>
                    '.$this->DrawUnitImage($unit).'
                </td><td class="gdDetailCol" valign=top>
                    '.$this->DrawUnitDetails($unit).'
                </td>
            </tr>
        </table>';
    }

    protected function DrawUnitImage($unit)
    {
/*        $note = (string)$unit['note'];
        if (strlen($note))
            $html .= '<br>Примечание: '.$note;
*/
        $link = $this->FormatLink('unit', $unit, $this->catalog);

        $img = str_replace('%size%', $this->size, $unit['imageurl']);
        if (strlen($img))
            $img = '<img class="img_group" src="'.$img.'">';

        return '
            <div class="guayaquil_unit_icons">
                <div class="guayaquil_zoom" link="'.$link.'" full="'.str_replace('%size%', 'source', $unit['imageurl']).'" title="'.$unit['code'].': '.$unit['name'].'"><img src="'.$this->zoom_image.'"></div>
            </div>
            <div class="gdImage'.(!strlen($img) ? ' gdNoImage' : '').'" style="width:'.(int)$this->size.'px; height:'.(int)$this->size.'px;">
               '.$img.'
            </div>
            <a href="'.$link.'"><b>'.$unit['code'].':</b> '.$unit['name'].'</a>
        ';
    }

    protected function DrawUnitDetails($unit)
    {
        return $this->detaillistrenderer->Draw($this->catalog, $unit->Detail);
    }

    protected function DrawEmptySet()
    {
        $link = $this->FormatLink('vehicle', null, $this->catalog);

        return 'Ничего не найдено, воспользуйтесь <a href="'.$link.'">иллюстрированным каталогом</a> для поиска требуемой детали';
    }
}
