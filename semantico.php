<?php
    $var = array();

    function pushVariavel($lex, $tip) {
        array_push($GLOBALS['var'], [
            'variavel' => $lex,
            'tipo' => $tip
        ]);
    }

    function vDecVariavel($next, $tipo)
    {
        if(inArrayPer($next['lexema'])){
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 5 , ' variável '."'".$next['lexema']."'".' declarada em duplicidade');   
        }
        pushVariavel($next['lexema'], $tipo);        
    }
    
    function inArrayPer($lex):bool
    {
        foreach ($GLOBALS['var'] as $var)
            if($var['variavel']===$lex) return true;
        return false;
    }

    // 3-4-6(5)

    // dd('sema');