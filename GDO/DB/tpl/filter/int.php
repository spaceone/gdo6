<?php /** @var $field \GDO\DB\GDT_Int **/ ?>
<input
 name="f[<?= $field->name; ?>]"
 type="text"
 pattern="^[-0-9]*$"
 value="<?= html($field->filterValue()); ?>"
 size="5" />
