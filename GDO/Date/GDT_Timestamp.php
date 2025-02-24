<?php
namespace GDO\Date;

use GDO\Core\GDT_Template;
use GDO\Core\GDT;
use GDO\UI\WithLabel;
use GDO\Form\WithFormFields;
use GDO\DB\WithDatabase;
use GDO\Table\WithOrder;
use GDO\Core\Application;

/**
 * The GDT_Timestamp field is the baseclass for all datefields.
 * The var type is a mysql date.
 * The value type is an integer/timestamp.
 *  
 * @author gizmore
 * @version 6.10.6
 * @since 6.0.7
 */
class GDT_Timestamp extends GDT
{
	use WithLabel;
	use WithFormFields;
	use WithDatabase;
	use WithOrder;
	use WithTimezone;
	
	public $icon = 'time';

	public $orderable = true;
	public $filterable = true;
	public $searchable = false;
	public $readable = true;
	public $editable = true;
	public $writable = true;
	
	public function isSerializable() { return true; }
	
	#############
	### Value ###
	#############
	public function toValue($var)
	{
	    if ($var)
	    {
	        return Time::parseDate($var);
	    }
	}
	
	public function toVar($value)
	{
		return $value === null ? null : Time::displayTimestamp($value, 'db', null, 'UTC');
	}
	
	public function getVar()
	{
		$var = trim(parent::getVar());
		return $var ? $var : null;
	}
	
	public function initialSnap($mod)
	{
		$time = Application::$TIME;
		$time = $time - ($time % $mod) + $mod;
		return $this->initialValue($time);
	}
	
	public function initialNow()
	{
	    return $this->initialAgo(0);
	}
	
	/**
	 * @param int $secondsAgo
	 * @return self
	 */
	public function initialAgo($secondsAgo)
	{
	    return $this->initial(Time::getDate(Application::$MICROTIME + $secondsAgo));
	}

	#####################
	### Starting view ###
	#####################
	public $dateStartView = 'month';
	public function startWithYear()
	{
		$this->dateStartView  = 'year';
		return $this;
	}
	public function startWithMonth()
	{
		$this->dateStartView  = 'month';
		return $this;
	}
	
	##############
	### Format ###
	##############
	public $format = Time::FMT_SHORT;
	public function format($key)
	{
		$this->format = $key;
		return $this;
	}
	
	###############
	### Min/Max ###
	###############
	/**
	 * @param int $duration
	 * @return \GDO\Date\GDT_Timestamp
	 */
	public function minAge($duration) { return $this->minTimestamp(Application::$TIME - $duration); }
	public function maxAge($duration) { return $this->maxTimestamp(Application::$TIME + $duration); }
	
	public $minDate;
	public function minTimestamp($minTimestamp)
	{
		return $this->minDate(Time::getDate($minTimestamp));
	}
	public function minDate($minDate)
	{
		$this->minDate = $minDate;		
		return $this;
	}
	
	public $maxDate;
	public function maxTimestamp($maxTimestamp)
	{
		return $this->maxDate(Time::getDate($maxTimestamp));
	}
	public function maxDate($maxDate)
	{
		$this->maxDate = $maxDate;
		return $this;
	}
	public function maxNow()
	{
	    return $this->maxDate(Time::getDate());
	}

	##############
	### Millis ###
	##############
	public $millis = true;
	public function millis($millis)
	{
	    $this->millis = $millis;
	    return $this;
	}
	
	###########
	### Now ###
	###########
	public $defaultNow = false;
	public function defaultNow($defaultNow=true)
	{
	    $this->defaultNow = $defaultNow;
	    return $this->initial(Time::getDate());
	}
	
	################
	### Validate ###
	################
	/**
	 * Validate a Datetime.
	 */
	public function validate($value)
	{
		if ( ($value === null) && (!$this->notNull) )
		{
			return true;
		}
		
		/** @var $value \DateTime **/
		if ($this->minDate !== null)
		{
		    if ($value->diff($this->minDate) < 0)
		    {
    		    return $this->error('err_min_date', [
    		        Time::displayDate($this->minDate, $this->getDateFormat())]);
		    }
		}
		
		if ($this->maxDate !== null)
		{
		    if ($value->diff($this->maxDate) > 0)
		    {
		        return $this->error('err_max_date', [
		            Time::displayDate($this->maxDate, $this->getDateFormat())]);
		    }
		}

		return parent::validate($value);
	}
	
	##############
	### Column ###
	##############
	public function gdoColumnDefine()
	{
		return "{$this->identifier()} TIMESTAMP{$this->gdoNullDefine()}{$this->gdoInitialDefine()}";
	}
	
	##############
	### Render ###
	##############
	public function displayVar() { return Time::displayDate($this->getVar(), $this->format); }
	public function renderCell() { return $this->renderCellSpan(Time::displayDate($this->getVar(), $this->format, '---')); }
	public function renderForm() { return GDT_Template::php('Date', 'form/datetime.php', ['field'=>$this]); }
	public function renderAge() { return Time::displayAge($this->getVar()); }
	public function renderCLI() { return $this->displayLabel() . ': ' . $this->displayVar(); }
	public function renderJSON() { return Time::getTimestamp($this->getVar()) * 1000; }
	
	/**
	 * Convert user input to db var date.
	 * If input is numeric it is a timestamp in ms.
	 */
	public function inputToVar($input)
	{
	    if (!is_numeric($input))
	    {
	        $input = str_replace('T', ' ', $input);
	        $input = str_replace('Z', '', $input);
	        $input = Time::parseDate($input);
	    }
	    else
	    {
	        $input /= 1000.0;
	    }
	    return $input ? Time::getDate($input) : null;
	}
	
	##############
	### Config ###
	##############
	public function configJSON()
	{
		return array_merge(parent::configJSON(), [
			'dateStartView' => $this->dateStartView,
			'format' => $this->getDateFormat(),
			'minDate' => $this->minDate,
			'maxDate' => $this->maxDate,
		    'millis' => $this->millis,
		]);
	}
	
	public function getDateFormat()
	{
	    if (!$this->format)
	    {
	        return $this->millis ? 'ms' : 'long';
	    }
	    return $this->format;
	}
	
	public function getDate()
	{
	    return Time::getDate($this->getValue());
	}
	
}
