<?php
namespace GDO\Date;

use GDO\Form\GDT_Select;

/**
 * Timezone select.
 *
 * @author gizmore
 * @version 6.10.3
 * @since 6.10.0
 */
final class GDT_Timezone extends GDT_Select
{
    public $max = 64;

    public function defaultName() { return 'timezone'; }
    public function defaultLabel() { return $this->label('timezone'); }

    protected function __construct()
    {
        parent::__construct();
        $this->notNull();
        $this->initChoices();
        $this->initial(GDO_TIMEZONE);
        $this->icon('time');
        $this->caseS();
        $this->ascii();
        $this->completionHref(href('Core', 'TimezoneComplete'));
        $this->searchable(false);
    }

    public function initChoices()
    {
        if (!$this->choices)
        {
            $tz = array_values(timezone_identifiers_list());
            $this->choices = array_combine($tz, $tz);
        }
        return $this;
    }

    public function plugVar()
    {
        return 'Europe/Berlin';
    }

}
