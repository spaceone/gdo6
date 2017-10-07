<?php
namespace GDO\User;
use GDO\DB\GDT_Object;
/**
 * An autocomplete enabled user field.
 * @author gizmore
 * @since 6.00
 * @version 6.05
 */
class GDT_User extends GDT_Object
{
    public function defaultLabel() { return $this->label('user'); }
    
    public function __construct()
    {
        $this->table(GDO_User::table());
        $this->withCompletion();
        $this->icon('face');
    }

    public function withCompletion()
    {
        return $this->completionHref(href('User', 'Completion'));
    }
    
    public function findByName($name)
    {
        if (!($user = GDO_User::getByName($name)))
        {
        }
        return $user;
    }
    
    private $ghost = false;
    public function ghost($ghost=true)
    {
        $this->ghost = $ghost;
        return $this;
    }
    
    /**
     * @return GDO_User
     */
    public function getUser()
    {
        if (!($user = $this->getValue()))
        {
            if ($this->ghost)
            {
                $user = GDO_User::ghost();
            }
        }
        return $user;
    }
    
    public function renderCell()
    {
        if ($user = $this->getUser())
        {
            return $user->displayName();
        }
        return t('unknown');
    }
}
