<?php
    $var = array();
    $exprecao = array();
    $tipoCompativel = null;

    $exp = null;
    $tip = null;

    function pushVariavel($lex, $tip) {
        array_push($GLOBALS['var'], [
            'variavel' => $lex,
            'tipo' => $tip
        ]);
    }

    function set($tip=null, $exp=null) {
        $GLOBALS['tip'] = $tip;
        $GLOBALS['exp'] = $exp;
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

    function setExpCompativel($next):void
    {
        if($next['token']==='id'){
            set(getTipo($next['lexema']));
        }

        if($next['token']==='numerico'){
            if(str_contains($next['valor'],'.')):
                set('real');
            else:                
                set('integer');
            endif;
        }
        
        if(verifRelacional($next['token'])){
            set($GLOBALS['tip'], 'rel');
        }

        if(verifBooleano($next['token'])){
            set($GLOBALS['tip'], 'bol');
        }

        if(verifAritmetico($next['token'])){
            set($GLOBALS['tip'], 'ari');
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
        if($GLOBALS['tipoCompativel']==='string'){
            if($next['token']!=='id')
                erro($next['lin'], $next['col'], verifSimboloInesp($next), 3 ,$GLOBALS['tipoCompativel']);

            if($next['token']==='id'&&(getTipo($next['lexema'])!=='string'))
                erro($next['lin'], $next['col'], getTipo($next['lexema']), 3 ,$GLOBALS['tipoCompativel']);
        }     
        
        if(($next['token']==='numerico')&&str_contains($next['valor'],'.')&&($GLOBALS['tipoCompativel']!=='real')) {
            erro($next['lin'], $next['col'], 'real', 3 ,$GLOBALS['tipoCompativel']);
        }
        if($GLOBALS['tipoCompativel']==='integer'|| $GLOBALS['tipoCompativel']==='real'){
            if($next['token']==='id'&&(getTipo($next['lexema'])==='string'))
                erro($next['lin'], $next['col'], 'string', 3 ,$GLOBALS['tipoCompativel']);                      
        }

        if($GLOBALS['tipoCompativel']==='integer'){            
            if($next['token']==='/')
                erro($next['lin'], $next['col'], 'real', 3 ,$GLOBALS['tipoCompativel']); 
        }

        if($GLOBALS['tipoCompativel']==='real'){            
            if(!verifAritmetico($next['token'])&&$next['token']!=='id'&&$next['token']!=='numerico')
                erro($next['lin'], $next['col'], verifSimboloInesp($next), 3 ,$GLOBALS['tipoCompativel']);

            if($next['token']==='id'&&(getTipo($next['lexema'])!=='real')&&(getTipo($next['lexema'])!=='integer'))
                erro($next['lin'], $next['col'], getTipo($next['lexema']), 3 ,$GLOBALS['tipoCompativel']);
        }
    }

    function tipoCompativelExp($next):void
    {        
        if($GLOBALS['tip']==='string'){
            if($next['token']==='id'&&getTipo($next['lexema'])!=='string')                
                erro($next['lin'], $next['col'], getTipo($next['lexema']), 3 ,$GLOBALS['tip']);            

            if($next['token']==='numerico')
                erro($next['lin'], $next['col'], str_contains($next['valor'],'.')?'real':'integer', 3 ,$GLOBALS['tip']);
                
            if($next['token']!=='numerico'&&$next['token']!=='id'&&$next['token']!=='=')
                erro($next['lin'], $next['col'], '', 3 ,$GLOBALS['tip']);            

            set($GLOBALS['tip'], 'rel');
        }     
        

        if($GLOBALS['tip']==='integer' || $GLOBALS['tip']==='real'){
            if($next['token']!=='numerico'&&$next['token']!=='id'&&!verifAritmetico($next['token'])&&!verifRelacional($next['token']))
                erro($next['lin'], $next['col'], verifSimboloInesp($next), 3 ,$GLOBALS['tip']);            
            
            if(verifRelacional($next['token'])) set($GLOBALS['tip'], 'rel');
            if(verifAritmetico($next['token'])) set($GLOBALS['tip'], 'ari');

            if($next['token']==='id'&&getTipo($next['lexema'])!=='integer' && getTipo($next['lexema'])!=='real'){                
                erro($next['lin'], $next['col'], getTipo($next['lexema']), 3 ,$GLOBALS['tip']);
            }
        }
        
    }

    function tipoCompativelAll($next):void
    {                        
        if(getTipo($next['lexema'])!=='string')
            erro($next['lin'], $next['col'], getTipo($next['lexema']), 3 ,'string');
    }
    // 3-4-6(5)

    // dd('sema');