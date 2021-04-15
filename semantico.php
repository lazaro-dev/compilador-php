<?php
    $var = array();
    $tipoCompativel = null;

    function pushVariavel($lex, $tip) {
        array_push($GLOBALS['var'], [
            'variavel' => $lex,
            'tipo' => $tip
        ]);
    }

    function vDecVariavel($next, $tipo)
    {
        if(inArrayPer($next['lexema'])){
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 5 , ' variável '."'".$next['lexema']."'".' declarada em duplicidade!');   
        }
        pushVariavel($next['lexema'], $tipo);
    }
    
    function inArrayPer($lex):bool
    {
        foreach ($GLOBALS['var'] as $var)
            if($var['variavel']===$lex) return true;
        return false;
    }

    function varNaoDeclarada($next):void
    {
        if(!inArrayPer($next['lexema'])){
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 4 , ' variável '."'".$next['lexema']."'".' não declarada!');
        }        
    }

    function setTipoCompativel($next):void
    {
        if($next){
            foreach ($GLOBALS['var'] as $var)
                if($var['variavel']===$next['lexema'])
                    $GLOBALS['tipoCompativel'] = $var['tipo'];
        }else{
            $GLOBALS['tipoCompativel'] = null;
        }
    }

    function getTipo($next):string
    {
        foreach ($GLOBALS['var'] as $var)
            if($var['variavel']===$next) 
                return $var['tipo'];
        return null;
    }

    function tipoCompativel($next):void
    {        
        // dd($GLOBALS['tipoCompativel']);
        if($GLOBALS['tipoCompativel']==='string'){
            if($next['token']!=='id')
                erro($next['lin'], $next['col'], verifSimboloInesp($next), 3 ,$GLOBALS['tipoCompativel']);

            if($next['token']==='id'&&(getTipo($next['lexema'])!=='string'))
                erro($next['lin'], $next['col'], getTipo($next['lexema']), 3 ,$GLOBALS['tipoCompativel']);
        }     
        
        if(($next['token']==='numerico')&&str_contains($next['valor'],'.')&&($GLOBALS['tipoCompativel']!=='real')){           
            erro($next['lin'], $next['col'], 'real', 3 ,$GLOBALS['tipoCompativel']);
        }
        if($GLOBALS['tipoCompativel']==='integer'|| $GLOBALS['tipoCompativel']==='real'){
            if($next['token']==='id'&&(getTipo($next['lexema'])==='string')){
                erro($next['lin'], $next['col'], 'string', 3 ,$GLOBALS['tipoCompativel']);
            }            
        }

        if($GLOBALS['tipoCompativel']==='integer'){            
            if($next['token']==='/'){
                erro($next['lin'], $next['col'], 'real', 3 ,$GLOBALS['tipoCompativel']);
            }
            //regra real
        }
        
    }

    // 3-4-6(5)

    // dd('sema');