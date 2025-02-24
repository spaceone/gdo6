<?php
namespace GDO\UI;

use GDO\Core\GDT;
use GDO\Core\GDT_Template;
use GDO\Core\WithFields;

/**
 * A dialog.
 * Very simple JS is used to display it.
 * Should almost work with CSS only.
 *
 * @author gizmore
 * @version 6.10
 * @since 6.10
 */
class GDT_Dialog extends GDT
{
    use WithTitle;
    use WithFields;
    use WithPHPJQuery;

	public function renderCell()
	{
		return GDT_Template::php('UI', 'cell/dialog', ['field' => $this]);
	}

	##############
	### Opened ###
	##############
	private $opened = false;

	/**
	 * Start dialog in open mode?
	 * @param boolean $opened
	 * @return \GDO\UI\GDT_Dialog
	 */
	public function opened($opened=true)
	{
	    $this->opened = $opened;
	    return $this;
	}

	#############
	### Modal ###
	#############
	/**
	 * Start dialog in modal mode?
	 * @var boolean
	 */
	public $modal = false;
	public function modal($modal=true)
	{
	    $this->modal = $modal;
	    return $this;
	}

}
