<?php

class No {

    public $esq;
    public $dir;
    public $val;

    public function __construct($val = '') {
        if ( $val != ' ' && !is_null($val) ) {
            $this->val = $val;
        }
    }

}