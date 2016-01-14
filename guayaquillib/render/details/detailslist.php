<?php

require_once dirname(__FILE__) . '/../template.php';

class GuayaquilDetailsList extends GuayaquilTemplate
{
    var $closedimage = 'images/closed.gif';
    var $opennedimage = 'images/openned.gif';
    var $fullreplacementimage = 'images/replacement.gif';
    var $forwardreplacementimage = 'images/replacement-forward.gif';
    var $backwardreplacementimage = 'images/replacement-backward.gif';
    var $detailinfoimage = 'images/info.gif';
    var $cartimage = 'images/cart.gif';

    var $columns = array('OEM' => 3, 'Name' => 3, 'Cart' => 1, 'Note' => 2, 'Tooltip' => 1);
    var $basic_columns = array('PNC' => 1, 'OEM' => 3, 'Name' => 3, 'Cart' => 1, 'Tooltip' => 1, 'flag' => 1, 'availability' => 1, 'Note' => 2,);

    var $currency = '%s';
    var $group_by_filter = false;
    var $row_class = '';
    static $table_no = 0;

    protected $details;
    protected $catalog;

    protected $rowno = 1;

    function __construct(IGuayaquilExtender $extender)
    {
        parent::__construct($extender);

        $this->closedimage              = $this->Convert2uri(dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->closedimage);
        $this->opennedimage             = $this->Convert2uri(dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->opennedimage);
        $this->fullreplacementimage     = $this->Convert2uri(dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->fullreplacementimage);
        $this->forwardreplacementimage  = $this->Convert2uri(dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->forwardreplacementimage);
        $this->backwardreplacementimage = $this->Convert2uri(dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->backwardreplacementimage);
        $this->detailinfoimage          = $this->Convert2uri(dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->detailinfoimage);
        $this->cartimage                = $this->Convert2uri(dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->cartimage);
    }

    function Draw($catalog, $details)
    {
        $this->catalog      = $catalog;
        $this->details      = $details;

        $this->AppendJavaScript(dirname(dirname(__FILE__)) . '/jquery.tooltip.js');
        $this->AppendJavaScript(dirname(__FILE__) . '/detailslist.js');

        $html = $this->DrawJavaScript();

        $html .= '<table class="guayaquil_table" width="96%" id="g_DetailTable' . (++GuayaquilDetailsList::$table_no) . '">';

        foreach ($this->basic_columns as $column => &$visibility) {
            $visibility = array_key_exists($column, $this->columns) ? $this->columns[$column] : 0; //?$this->columns[$column]:$visibility;
        }

        $html .= $this->DrawHeader($this->basic_columns);
        $this->row_class = '';
        $found           = false;
        foreach ($details as $detail) {
            $detail['note'] = '';
            foreach ($detail->attribute as $attr) {
                $detail['note'] .= '<span class="item"><span class="name">' . (string)$attr->attributes()->name . '</span><span class="value">' . (string)$attr->attributes()->value . '</span></span>';
            }
        }
        if ($this->group_by_filter) {
            $searched   = array();
            $additional = array();
            foreach ($details as $detail) {
                $found = true;
                if ((string)$detail['match']) {
                    $searched[] = $detail;
                } else {
                    $additional[] = $detail;
                }
            }

            if (!$found)
                foreach ($details->row as $detail) {
                    if ((string)$detail['match']) {
                        $searched[] = $detail;
                    } else {
                        $additional[] = $detail;
                    }
                }

            foreach ($searched as $detail) {
                $html .= $this->DrawItem($catalog, $detail);
                $this->rowno++;
            }

            $html .= $this->DrawAdditionalItemSplitter();
            $this->row_class .= ' g_addgr g_addgr_collapsed ';

            foreach ($additional as $detail) {
                $html .= $this->DrawItem($catalog, $detail);
                $this->rowno++;
            }
        } else {
            foreach ($details as $detail) {
                $found = true;

                $html .= $this->DrawItem($catalog, $detail);
                $this->rowno++;
            }

            if (!$found)
                foreach ($details->row as $detail) {
                    $html .= $this->DrawItem($catalog, $detail);
                    $this->rowno++;
                }
        }

        $html .= '</table>';

        if ($this->rowno == 1)
            $html = $this->DrawEmptySet();

        return $html;
    }

    function DrawItem($catalog, $detail)
    {
        $html        = $this->DrawDetailRow($detail, $this->columns, false, $catalog);

        return $html;
    }

    function DrawJavaScript()
    {
        $html = "<script type=\"text/javascript\">\n";
        $html .= "var opennedimage = '" . $this->opennedimage . "'; \n";
        $html .= "var closedimage = '" . $this->closedimage . "'; \n";
        $html .= "jQuery(document).ready(function($){ \n";
        $html .= "jQuery('td.g_rowdatahint').tooltip({track: true, delay: 0, showURL: false, fade: 250, positionLeft: true, bodyHandler: g_getHint}); \n";

        $html .= "jQuery('img.g_addtocart').tooltip({track: true, delay: 0, showURL: false, fade: 250, bodyHandler: function() { return '" . $this->GetLocalizedString("AddToCartHint") . "'; } }); \n";

        $html .= "jQuery('td[name=c_toggle] img').tooltip({track: true, delay: 0, showURL: false, fade: 250, bodyHandler: function() { return '" . $this->GetLocalizedString("ToggleReplacements") . "'; } }); \n";

        $html .= "jQuery('img.c_rfull').tooltip({track: true, delay: 0, showURL: false, fade: 250, bodyHandler: function() { return '<h3>" . $this->GetLocalizedString("ReplacementWay") . "</h3>" . $this->GetLocalizedString("ReplacementWayFull") . "'; } }); \n";

        $html .= "jQuery('img.c_rforw').tooltip({track: true, delay: 0, showURL: false, fade: 250,	bodyHandler: function() { return '<h3>" . $this->GetLocalizedString("ReplacementWay") . "</h3>" . $this->GetLocalizedString("ReplacementWayForward") . "'; } }); \n";

        $html .= "jQuery('img.c_rbackw').tooltip({track: true, delay: 0, showURL: false, fade: 250, bodyHandler: function() { return '<h3>" . $this->GetLocalizedString("ReplacementWay") . "</h3>" . $this->GetLocalizedString("ReplacementWayBackward") . "'; } }); \n";
        $html .= "});\n";
        $html .= "</script>\n";

        return $html;
    }

    function getProperty($detail, $propertyName, $isArray)
    {
        if ($isArray) {
            return $detail->$propertyName;
        }

        return $detail[$propertyName];
    }

    function DrawEmptySet()
    {
        return $this->GetLocalizedString('nothingfound');
    }

    function DrawDetailRow($detail, $columns, $isArray, $catalog)
    {
        $html = '<tr';

        //$detail->addAttribute('filter', '*');
        $filter = $this->getProperty($detail, 'filter', $isArray);
        $bits   = (int)$this->getProperty($detail, 'flag', $isArray);

        $pnc = $this->getProperty($detail, 'codeonimage', $isArray);

        $html .= ' class="' . $this->row_class . (strlen($filter) > 0 ? 'g_filter_row ' : '') . ($bits ? ' g_nonstandarddetail ' : '') . 'g_collapsed g_highlight"';
        $html .= ' name="' . (isset($pnc) ? $pnc : 'd_' . $this->rowno) . '"';
        $html .= ' id="d_' . $this->rowno . '"';
        $html .= ' onmouseout="hl(this, \'out\');" onmouseover="hl(this, \'in\');">';

        foreach ($this->basic_columns as $column => $visibility)
            $html .= $this->DrawDetailCell($detail, strtolower($column), $visibility, $isArray);

        $html .= '</tr>';

        return $html;
    }

    function DrawDetailCell($detail, $column, $visibility, $isArray)
    {

        $html = '<td name="c_' . $column . '"';
        if (($visibility & 1) == 0) {
            $html .= ' style="display:none;"';
        }
        if ($column == 'tooltip') {
            $html .= ' class="g_rowdatahint"';
        } elseif (($visibility & 2) > 0) {
            $html .= ' class="g_ttd"';
        }
        $html .= '>';

        $html .= $this->DrawDetailCellValue($detail, $column, $visibility, $isArray);

        $html .= '</td>';
        return $html;
    }

    function GetFilterURL($detail, $isArray)
    {
        $filter = $this->getProperty($detail, 'filter', $isArray);
        if (!strlen($filter)) {
            return false;
        }

        return $this->FormatLink('filter', $detail, $this->catalog);
    }

    function DrawDetailCellValue($detail, $column, $visibility, $isArray)
    {
        switch ($column) {
            case 'pnc':
                return $this->getProperty($detail, 'codeonimage', $isArray);

            case 'oem':
                return $this->getProperty($detail, 'oem', $isArray);

            case 'amount':
                return $this->getProperty($detail, 'amount', $isArray);

            case 'name':
                $link = $this->FormatLink('detail', $detail, $this->catalog);

                $html = '<a href="'.$link.'">';
                $name = $this->getProperty($detail, 'name', $isArray);
                if (!strlen((string)$name)) {
                    $name = 'Наименование не указано';
                }

                $html .= ' ' . $name .'</a>';
                return $html;

            case 'cart':
                return '<img class="g_addtocart" src="' . $this->cartimage . '" width="22" height="22">';

            case 'note':
                return str_replace("\n", "<br>", $this->getProperty($detail, 'note', $isArray));

            case 'tooltip':
                return '<img src="' . $this->detailinfoimage . '" width="22" height="22">';

            case 'flag':
                $bits  = (int)$this->getProperty($detail, 'flag', $isArray);
                $flags = '';
                if ($bits & 1 > 0) {
                    $flags .= 'Нестандартная деталь';
                }
                return $flags;

            default:
                return str_replace("\n", "<br>", $this->getProperty($detail, $column, $isArray));
        }
    }

    function DrawHeader($columns)
    {
        $html = '<tr>';

        foreach ($columns as $column => $visibility) {
            $html .= $this->DrawHeaderCell(strtolower($column), $visibility);
        }

        $html .= '</tr>';

        return $html;
    }

    function DrawHeaderCell($column, $visibility)
    {
        return '<th id="c_' . $column . '"' . (($visibility & 1) == 0 ? ' style="display:none;"' : '') . '>' . $this->DrawHeaderCellValue($column, $visibility) . '</th>';
    }

    function DrawHeaderCellValue($column, $visibility)
    {
        switch ($column) {
            case 'toggle':
                return '&nbsp;';

            case 'pnc':
                return $this->GetLocalizedString('ColumnDetailCodeOnImage');

            case 'oem':
                return $this->GetLocalizedString('ColumnDetailOEM');

            case 'amount':
                return $this->GetLocalizedString('ColumnDetailAmount');

            case 'name':
                return $this->GetLocalizedString('ColumnDetailName');

            case 'cart':
                return '&nbsp;';

            case 'price':
                return $this->GetLocalizedString('ColumnDetailPrice');

            case 'note':
                return $this->GetLocalizedString('ColumnDetailNote');

            case 'tooltip':
                return '&nbsp;';

            case 'availability':
                return $this->GetLocalizedString('WhereToBuy');

            default:
                return $this->GetLocalizedString('ColumnDetail' . $column);
        }

        return '';
    }

    function DrawAdditionalItemSplitter()
    {
        return '<tr>
            <td colspan="' . count($this->columns) . '">
                <img class="g_additional_toggler g_addcollapsed" class="g_addcollapsed" src="' . $this->closedimage . '" width="16" height="16" onclick="g_toggleAdditional(\'g_DetailTable' . GuayaquilDetailsList::$table_no . '\', opennedimage, closedimage);">
                <a href="#" onClick="g_toggleAdditional(\'g_DetailTable' . GuayaquilDetailsList::$table_no . '\', opennedimage, closedimage); return false;"> '.$this->GetLocalizedString('otherunitparts').'</a>
            </td>
        </tr>';
    }
}