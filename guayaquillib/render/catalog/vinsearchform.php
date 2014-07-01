<?php

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'template.php';

class GuayaquilVinSearchForm extends GuayaquilTemplate
{
	var $catalog = NULL;
	var $cataloginfo = NULL;

	function __construct(IGuayaquilExtender $extender)
	{
		parent::__construct($extender);
	}

	function Draw($catalog, $cataloginfo, $prevvin = '')
	{
		$this->cataloginfo = $cataloginfo;
		$this->catalog = $catalog;

		$html  = $this->DrawVinCheckScript();
		$html .= $this->DrawVinExample($cataloginfo);
		$html .= $this->DrawVinForm($catalog, $prevvin);

		return $html;
	}

	function DrawVinCheckScript()
	{
		$html  = '<script type="text/javascript"> 
		function checkVinValue(value, submit_btn) {
		    value = value.replace(/[^\da-zA-Z]/g,\'\');
            var expr = new RegExp(\''.$this->GetVinRegularExpression().'\', \'i\');
            if (expr.test(value))
            {
                jQuery(submit_btn).attr(\'disabled\', \'1\');
                jQuery(\'#VINInput\').attr(\'class\',\'g_input\');
                window.location = \''.$this->FormatLink('vehicles', NULL, $this->catalog).'\'.replace(\'\\$vin\\$\', value);
            } else
            jQuery(\'#VINInput\').attr(\'class\',\'g_input_error\');
        }
		</script> ';

		return $html;
	}

	function GetVinRegularExpression()
	{
		return '\\^[A-z0-9]{12}[0-9]{5}\$';
	}

	function GetVinExample($cataloginfo)
	{
        $example = $cataloginfo['vinexample'];
        if ($example)
            return $example;

        $code2 = substr($cataloginfo['code'], 0, 2);
        $code6 = substr($cataloginfo['code'], 0, 6);
        if (!$code2)
            return 'WAUBH54B11N111054';

		if ($code2 == 'he')
			return'1HGCB7543LA000002';
		else if ($code2 == 'ki')
			return'KNEBA24428T522301';
		else if ($code2 == 'hy')
			return'KMHVD34N8VU263043';
		else if ($code2 == 'te')
			return'SB164SBK10E032155';
		else if ($code2 == 'tg')
			return'JT111PJA508001249';
		else if ($code2 == 'tu')
			return'1NXBR32E53Z094412';
		else if ($code2 == 'ne')
			return'3N1BC13E07L364030';
		else if ($code2 == 'ie')
			return'5N3AA08C04N811146';
		else if ($code2 == 'mg')
			return'JM7GG32F141127052';
		else if ($code2 == 'me')
			return'JM7BK326081421458';
        else if ($code2 == 'AU')
            return'WAUBH54B11N111054';
        else if ($code2 == 'VW')
            return'WVWZZZ7MZ7V006700';
        else if ($code2 == 'SK')
            return'TMBCA21Z962131685';
        else if ($code2 == 'SE')
            return'VSSZZZ9KZ1R003158';
        else if ($code6 == 'MMCM60')
            return'XMCLNDA1A3F016543';
        else if ($code6 == 'MMCM50')
            return'4A3AC54L3YE163458';
        else if ($code6 == 'MMCM80')
            return'MMCJNKB409D008733';
        else if ($code2 == 'MB')
            return'WDBUF65J14A605034';

    }

    function DrawVinExample($cataloginfo)
    {
		return $this->GetLocalizedString('InputVIN', array($this->GetVinExample($cataloginfo))).'<br>';
	}

	function DrawVinForm($catalog, $prevvin)
	{
        $html  = '
            <form name="findByVIN" onSubmit="checkVinValue(this.vin.value);return false;" id="findByVIN" >
                <div id="VINInput" class="g_input"><input name="vin" type="text" id="vin" size="17" style="width:200px;" value="'.$prevvin.'"/></div>
                <input type="submit" name="vinSubmit" value="'.$this->GetLocalizedString('Search').'" id="vinSubmit" />
                <input type="hidden" name="option" value="com_guayaquil" />
                <input type="hidden" name="view" value="vehicles" />
                <input type="hidden" name="ft" value="findByVIN" />
		    </form>';

		return $html;
	}
}
