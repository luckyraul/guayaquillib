<?php

require_once('soap.php');

class GuayaquilRequest
{
	//	Function parameters
	public $locale;

	// Temporery varibles
	public $query = '';

	// soap wrapper object
	public $soap;

	//	Results
	public $error;
	public $data;

    public $certificatePath;

	function __construct($locale = 'ru_RU')
	{
		$this->locale = $this->checkParam($locale);
        $this->soap = new GuayaquilSoapWrapper();
	}

	function checkParam($value)
	{
		return $value;
	}

	function appendCommand($command)
	{
		if ($this->query == '')
			$this->query = $command;
		else
			$this->query .= "\n".$command;
	}

    public function appendFindOEM($oem, $options, $brand = null, $replacementtypes = 'default')
    {
        $this->appendCommand('FindOEM:Locale='.$this->locale.'|OEM='.$oem.'|ReplacementTypes='.$replacementtypes.'|Options='.$options.($brand ? '|Brand='.$brand : ''));
    }

    public function appendFindOEMCorrection($oem)
    {
        $this->appendCommand('FindOEMCorrection:Locale='.$this->locale.'|OEM='.$oem);
    }

    public function appendFindDetail($id, $options, $replacementtypes = 'default')
    {
        $this->appendCommand('FindDetail:Locale='.$this->locale.'|DetailId='.$id.'|ReplacementTypes='.$replacementtypes.'|Options='.$options);
    }

    public function appendManufacturerInfo($id)
    {
        $this->appendCommand('ManufacturerInfo:Locale='.$this->locale.'|ManufacturerId='.$id);
    }

    public function appendListManufacturer()
    {
        $this->appendCommand('ListManufacturer:Locale='.$this->locale);
    }

    public function appendFindReplacements($id)
    {
        $this->appendCommand('FindReplacements:Locale='.$this->locale.'|DetailId='.$id);
    }

	function query()
	{
        if ($this->certificatePath != '')
            $this->soap->certificatePath = $this->certificatePath;

		$this->soap->queryData($this->query, false);

		$this->query = '';

		$this->error = $this->soap->error;
		$this->data = $this->soap->data;

		return $this->data;
	}
}
?>