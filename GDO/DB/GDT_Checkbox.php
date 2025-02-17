<?php
namespace GDO\DB;

use GDO\Form\GDT_Select;
use GDO\Core\GDT_Template;

/**
 * Boolean Checkbox.
 * Implemented as select to reflect undetermined status. Also HTML does not send unchecked boxes over HTTP.
 * 
 * @TODO what about real checkboxes? Not a single one wanted/needed?
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 5.0.0
 */
class GDT_Checkbox extends GDT_Select
{
    # db var representation. '2' is the third state.
    const TRUE = '1';
    const FALSE = '0';
    const UNDETERMINED = '2';

    public $searchable = false;

	protected function __construct()
	{
	    parent::__construct();
		$this->emptyValue = '2';
// 		$this->min = 0;
// 		$this->max = 1;
		$this->ascii(); # This enables string search (not binary).
		$this->caseS();
	}

	public function initChoices()
	{
		if (!$this->choices)
		{
			$this->choices([
				'0' => t('enum_no'),
				'1' => t('enum_yes'),
			]);
			if ($this->undetermined)
			{
				$this->emptyInitial(t('please_choose'), $this->emptyValue);
				$this->choices[$this->emptyValue] = $this->displayEmptyLabel();
			}
		}
		return $this;
	}

	################
	### Database ###
	################
	/**
	 * Get TINYINT(1) column define.
	 * {@inheritDoc}
	 * @see \GDO\DB\GDT_String::gdoColumnDefine()
	 */
	public function gdoColumnDefine()
	{
		return "{$this->identifier()} TINYINT(1) UNSIGNED ".
		  "{$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
	}

	/**
	 * Return no collation for a tinyint.
	 * {@inheritDoc}
	 * @see \GDO\DB\GDT_String::gdoCollateDefine()
	 */
	public function gdoCollateDefine($caseSensitive)
	{
	    return '';
	}

	####################
	### Undetermined ###
	####################
	public $undetermined = false;
	public function undetermined($undetermined=true)
	{
	    $this->max = $undetermined ? 2 : 1;
		$this->undetermined = $undetermined;
		return $this;
	}

	###################
	### Var / Value ###
	###################
	public function toVar($value)
	{
		if ($value === true) { return '1'; }
		elseif ($value === false) { return '0'; }
		else { return null; }
	}

	public function toValue($var)
	{
		if ($var === '0') { return false; }
		elseif ($var === '1') { return true; }
		else { return null; }
	}

	################
	### Validate ###
	################
	public function validate($value)
	{
		$this->initChoices();
		if ($value === true)
		{
		    return true;
		}
		if ($value === false)
		{
		    return true;
		}
		if ($value === null)
		{
		    return parent::validate($value);
		}
		return $this->errorInvalidChoice();
	}

	protected function errorInvalidVar($var)
	{
	    return t('err_invalid_gdt_var', [$this->gdoHumanName(), html($var)]);
	}

	public function gdoExampleVars()
	{
	    return '0|1';
	}

	##############
	### Render ###
	##############
	public function displayValue($var)
	{
	    if ($var === null)
	    {
	        return t('enum_undetermined_yes_no');
	    }
	    switch ($var)
	    {
	        case '0': return t('enum_no');
	        case '1': return t('enum_yes');
	        case '2': return t('enum_undetermined_yes_no');
	        default: return $this->errorInvalidVar($var);
	    }
	}

	public function htmlClass()
	{
		return parent::htmlClass() . " gdt-checkbox-{$this->getVar()}";
	}

	public function renderForm()
	{
		$this->initChoices();
		$this->initThumbIcon();
		return parent::renderForm();
	}

	public function renderCell()
	{
	    return $this->displayValue($this->getVar());
	}

	public function renderJSON()
	{
	    return $this->displayValue($this->getVar());
	}

	public function renderFilter($f)
	{
	    $vars = ['field' => $this, 'f'=> $f];
		return GDT_Template::php('DB', 'filter/checkbox.php', $vars);
	}

	####################
	### Dynamic Icon ###
	####################
	/**
	 * Init label icon with thumb up or thumb down.
	 * @return \GDO\DB\GDT_Checkbox
	 */
	private function initThumbIcon()
	{
	    switch ($this->getVar())
	    {
	        case '0': return $this->icon('thumbs_down');
	        case '1': return $this->icon('thumbs_up');
	        default: return $this->icon('thumbs_none');
	    }
	}

}
