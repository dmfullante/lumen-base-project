<?php

namespace App\Traits;

trait HasPersonName
{
    /**
     * Return the full concatenated names.
     * Note: Must have the same format with $this->fullName()
     *
     * @return string
     */
    public function fullName()
    {
        $middleName = $this->middle_name ? substr($this->middle_name,0,1).". ":"";
        return strtoupper(trim("{$this->last_name}, {$this->first_name} {$middleName}{$this->suffix}"));
    }

    /**
     * Select statement for full name.
     * Note: Must have the same format with $this->selectFullName()
     *
     * @return string
     */
    public function selectFullName()
    {
        $table = $this->getTable();
        return "TRIM(CONCAT(".
            "{$table}.last_name, ', ', ".
            "{$table}.first_name, ' ',".
            "IFNULL({$table}.middle_name, ''), ' ',".
            "IFNULL({$table}.suffix, '')".
            ")) AS full_name";
    }
}
