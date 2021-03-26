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

        $next = nextToken(fgets($GLOBALS['f']));
        $counToken++;
        if($counToken===2) {
            $next = vPrograma($token, $next);
        }

        while(eTipo($next['token'])){
           $next = vDeclaracaoVariaveis(nextToken(fgets($GLOBALS['f'])));
        }
        $next = vBegin($next);
        
        if($next['token']==='id'){
            vAtribuicao(nextToken(fgets($GLOBALS['f'])));
        }
            dd();
    }

    function vPrograma($token, $next){
        if(($token['token'] !== 'programa')){
            erro($token['lin'], $token['col'], verifSimboloInesp($token), 2 , 'esperando \'programa\'');
        }
        if(($next['token'] !== 'id')){
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , 'esperando \'ID\'');
        }else{
            $next = nextToken(fgets($GLOBALS['f']));
            if($next['token'] !== ';') {
                erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , 'esperando \';\'');
            }
        }
        return nextToken(fgets($GLOBALS['f']));
    }

    function vDeclaracaoVariaveis($next) {
        while($next['token'] !== ';'){
            if($next['token'] !== "id" && $next['token'] !== ','){
                erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , 'esperando \'begin\' ou \'(string|real|inteiro)\' ');
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
               erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , 'esperando \'begin\'');
            }
        }else {
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , 'esperando \'begin\'');
        } 
        return $next;
    }

    function vAtribuicao($next) {
        if($next['token']!==':=') {             
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , 'esperando \':=\'');
        }
        $next = nextToken(fgets($GLOBALS['f']));
        if($next['token'] === ';') erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , 'esperando \'id\' ou \'numerico\'');
        $cout = 0;
        $i = 0;
        while($next['token'] !== ';'){
            if($next['token']==='id'){
                $next = nextToken(fgets($GLOBALS['f']));
                if(!verifAritmetico($next['token'])) {
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , 'esperando \'(-+*/)\'');
                }
            }

            if(verifAritmetico($next['token'])) {
                $next = nextToken(fgets($GLOBALS['f']));
                if($next['token']!=='id' &&$next['token']!=='numerico' &&$next['token']!=='(') {
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , 'esperando \'id\' ou \'numerico\'');
                }
            }
            
            if($next['token']==='('){
                ++$cout;                
                $next = nextToken(fgets($GLOBALS['f']));
                if($next['token']!=='id' &&$next['token']!=='numerico' &&$next['token']!=='('&&$next['token']!==')'&&$next['token']!=='-') {
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , 'esperando \'id\' ou \'numerico\'');
                }
            }

            if($next['token']==='numerico'){                
                $next = nextToken(fgets($GLOBALS['f']));
                if($next['token']!=='('&&$next['token']!==')'&&!verifAritmetico($next['token'])&&$next['token']!==';') {
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , 'esperando \'(\' , \')\' ou (+-*/)');
                }
            }

            if($next['token']===')') {
                --$cout;                
                $next = nextToken(fgets($GLOBALS['f']));
                if($next['token']!==')'&&!verifAritmetico($next['token'])&&$next['token']!==';') {                    
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , 'esperando \';\' ou (+-/*)');
                }
            }

            $i++;
            // if($i===10) dd($next);
            //dd($next);
        }
        if($cout!==0) {
            // dd($cout);
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , 'esperando \')\'');
        }
        return nextToken(fgets($GLOBALS['f']));
    }

    //valor de variavel string
    