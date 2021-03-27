<?php

    $path = './tabelas/lexica/tabela.txt';
    $f = fopen($path,'r');

    // function getToken()
    // {
    //     $linha = fgets($GLOBALS['f']);
    //     $arg = explode(' | ', trim($linha));
    //     return [
    //         'token' => $arg[0],
    //         'lin' => $arg[3],
    //         'col' => $arg[4]
    //       ];
    // }

    function verifSimboloInesp($token)
    {
        if($token['valor']!=="") return $token['valor'];
        if($token['lexema']!=="") return $token['lexema'];
        if($token['token']!=="") return $token['token'];
    }
    function nextToken($linha)
    {
        $arg = explode(' | ', trim($linha));
        return [
            'token' => $arg[0],
            'lexema' => $arg[1],
            'valor' => $arg[2],
            'lin' => $arg[3],
            'col' => $arg[4]
        ];
    }
    $token =null;
    $counToken = 0;

    while($linha = fgets($GLOBALS['f'])){
        if($token===null){
            $token = nextToken($linha);
            $counToken++;
        }
        $counToken++;
        // if($counToken=== 3) dd($next );
        if($counToken===2) {
            $next = nextToken(fgets($GLOBALS['f']));
            $next = vPrograma($token, $next);
        }else{
            $next = nextToken($linha);
        }
        
        while(eTipo($next['token'])){
           $next = vDeclaracaoVariaveis(nextToken(fgets($GLOBALS['f'])));
           
           if($next['token']==='begin'){               
               $next = vBegin($next);
           }
        }
      
        if($next['token']==='id'){         
            $next = vAtribuicao(nextToken(fgets($GLOBALS['f'])));                
        }
        
        if($next['token']==='if'){
            $next = vIf(nextToken(fgets($GLOBALS['f'])));
        }
        if($next['token']==='while'){
            $next = vWhile($next);            
        }
    }

    function vPrograma($token, $next){
        if(($token['token'] !== 'programa')){
            erro($token['lin'], $token['col'], verifSimboloInesp($token), 2 , ' \'programa\'');
        }
        if(($next['token'] !== 'id')){
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'ID\'');
        }else{
            $next = nextToken(fgets($GLOBALS['f']));
            if($next['token'] !== ';') {
                erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \';\'');
            }
        }
        return nextToken(fgets($GLOBALS['f']));
    }

    function vDeclaracaoVariaveis($next) {
        while($next['token'] !== ';'){
            if($next['token'] !== "id" && $next['token'] !== ','){
                erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'begin\' ou \'(string|real|inteiro)\' ');
            }
            $next = nextToken(fgets($GLOBALS['f']));
        }
        return nextToken(fgets($GLOBALS['f']));
    }

    function vBegin($next) {        
        if($next['token']==='begin') {
            $next = nextToken(fgets($GLOBALS['f']));
            if($next['token']==='begin') {
                $next = nextToken(fgets($GLOBALS['f']));        
            }else {
               erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'begin\'');
            }
        }else {
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'begin\'');
        } 
        return $next;
    }

    function vAtribuicao($next) {
        
        if($next['token']!==':=') {             
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \':=\'');
        }
        $next = nextToken(fgets($GLOBALS['f']));
        if($next['token'] === ';') erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'id\' ou \'numerico\'');
        $cout = 0;
        while($next['token'] !== ';'){
            if($next['token']==='id'){
                $next = nextToken(fgets($GLOBALS['f']));
                if(!verifAritmetico($next['token'])&&$next['token'] !== ';'&&$next['token'] !== ')') {
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'(-+*/)\'');
                }
            }

            if(verifAritmetico($next['token'])) {
                $next = nextToken(fgets($GLOBALS['f']));
                if($next['token']!=='id' &&$next['token']!=='numerico' &&$next['token']!=='(') {
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'id\' ou \'numerico\'');
                }
            }
            
            if($next['token']==='('){
                ++$cout;                
                $next = nextToken(fgets($GLOBALS['f']));
                if($next['token']!=='id' &&$next['token']!=='numerico' &&$next['token']!=='('&&$next['token']!==')'&&$next['token']!=='-') {
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'id\' ou \'numerico\'');
                }
            }
                      
            if($next['token']==='numerico'){ 
                $next = nextToken(fgets($GLOBALS['f']));
                if($next['token']!==')'&&!verifAritmetico($next['token'])&&$next['token']!==';') {
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'(\' , \')\' ou (+-*/)');
                }
            }

            if($next['token']===')') {
                --$cout;                
                $next = nextToken(fgets($GLOBALS['f']));
                if($next['token']!==')'&&!verifAritmetico($next['token'])&&$next['token']!==';') { 
                    // dd();                   
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \';\' ou (+-/*)');
                }
            }
            if($next['token']!=='id'&&$next['token']!==')'&&$next['token']!=='('&&!verifAritmetico($next['token'])&&$next['token']!=='numerico'&&$next['token']!==';') {
                erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'id\' ou \'numerico\'');
            }
        }
        if($cout!==0) {            
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \')\'');
        }
        // dd($next);
        return $next;
    }

    function vWhile($next) {
        dd($next);
        return nextToken(fgets($GLOBALS['f']));
    }

    function vIf($next, $PARA='then') {
        $cout = 0;
        
        $ari = false;
        $rel = false;
        $dent = false;
        
        if($next['token']!=='(') {
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'(\'');
        }
        $next = nextToken(fgets($GLOBALS['f']));
        
        if($next['token']!=='id'&&$next['token']!=='('&&$next['token']!=='numerico') {
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'id\' ou \'numerico\'');
        }
        while($next['token']!==$PARA) {
            if($next['token']==='id'){
                $next = nextToken(fgets($GLOBALS['f']));
                if(!verifRelacional($next['token'])&&$next['token'] !== ')'&&!verifAritmetico($next['token'])) {
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \')\' ou \'(relacional)\'');
                }
                if((verifRelacional($next['token'])&&$rel)||(verifAritmetico($next['token'])&&$ari)){
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \')\' ou \'(expressão valida)\'');
                }                
            }

            if(verifRelacional($next['token'])) {                
                $rel = true;
                $next = nextToken(fgets($GLOBALS['f']));
                if($next['token']!=='id' &&$next['token']!=='numerico'&&$next['token']!=='(') {
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'id\' ou \'numerico\'');
                }
            }

            if(verifBooleano($next['token'])) {
                $next = nextToken(fgets($GLOBALS['f']));
                if($next['token']!=='(') {
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , '  \'(\'');
                }
            }

            if(verifAritmetico($next['token'])) {
                $ari = true;
                $next = nextToken(fgets($GLOBALS['f']));
                if($next['token']!=='id' &&$next['token']!=='numerico'&&$next['token']!=='(') {
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , '  \'id\' ou \'numerico\'');
                }
            }
            
            if($next['token']==='('){
                ++$cout;      
                $dent = true;
                $next = nextToken(fgets($GLOBALS['f']));
                if($next['token']!=='id' &&$next['token']!=='numerico' &&$next['token']!=='(') {//&&$next['token']!=='-'
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'id\' ou \'numerico\'');
                }
            }
                      
            if($next['token']==='numerico'){ 
                $next = nextToken(fgets($GLOBALS['f']));
                if($next['token']!==')'&&!verifRelacional($next['token'])&&!verifAritmetico($next['token'])) {
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \')\' , \'(relacional)\'');
                }
                if((verifRelacional($next['token'])&&($rel||$ari)&&$dent)||(verifAritmetico($next['token'])&&$rel&&$dent&&$ari)){
                    // dd((verifAritmetico($next['token'])&&$rel&&$dent));
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \')\' ou \'(expressão valida)\'');
                }      
            }

            if($next['token']===')') {
                $next = nextToken(fgets($GLOBALS['f']));
                
                if(verifBooleano($next['token'])&&$ari){
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'(expressão valida)\'');
                }
                if(verifRelacional($next['token'])&&$rel){
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'(expressão valida)\'');
                }
                if(verifAritmetico($next['token'])&&$rel){
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'(expressão valida)\'');
                }
                $rel = false;
                $ari = false;
                $dent = false;
                if($next['token']===$PARA){
                }else{
                    --$cout;
                }
                if($cout<0) erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'()\'');

                if($next['token']!==')'&&!verifBooleano($next['token'])&&$next['token']!==$PARA&&!verifAritmetico($next['token'])&&!verifRelacional($next['token'])) {                 
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \';\' ou (boolean)');
                }
            }

        }
        
        if($cout!==0) {   
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'()\'');
        }
        // dd($next);
        return nextToken(fgets($GLOBALS['f']));
    }

    function vRepeat($next) {
        return nextToken(fgets($GLOBALS['f']));
    }
    