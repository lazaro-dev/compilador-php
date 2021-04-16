<?php

    $path = './tabelas/lexica/tabela.txt';
    $f = fopen($path,'r');   

    $token =null;
    $counToken = 0;

    while($linha = fgets($GLOBALS['f'])) {
        if($token===null) {
            $token = nextToken($linha);
            $counToken++;
        }
        $counToken++;
        if($counToken===2) {
            $next = nextToken(fgets($GLOBALS['f']));
            $next = vPrograma($token, $next);
        }else{
            $next = nextToken($linha);
        }
        
        while(eTipo($next['token'])) {
            $next = vDeclaracaoVariaveis(nextToken(fgets($GLOBALS['f'])), $next['token']);            
        }
        if($next['token']==='begin') {
            $next = vBegin($next);
            $next = bloco($next);
            if($next['token']!=='end'){
                erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'end\'');
            }else{
                $next = vEnd(nextToken(fgets($GLOBALS['f'])));
                $arg = explode(' | ', trim(fgets($GLOBALS['f'])));
                if($arg[0]!=="") {                        
                    erro($arg[3]+1, $arg[4], verifSimboloInesp(['valor'=>$arg[0]]), 2 , ' \'fim do programa\'');
                }
            }
        }
    }
    dd("SEM ERROS!?");
    dd($GLOBALS['var']);

    function bloco($next)
    {
        if($next['token']==='if'){            
            $next = vIf(nextToken(fgets($GLOBALS['f'])));
        }
        if($next['token']==='while'){          
            $next = vWhile(nextToken(fgets($GLOBALS['f'])));
        }
        if($next['token']==='id') {
            //semantico
            varNaoDeclarada($next);
            setTipoCompativel($next);            
            $next = vAtribuicao(nextToken(fgets($GLOBALS['f'])));
        }
        if($next['token']==='all') {
            $next = vAll(nextToken(fgets($GLOBALS['f'])));
        }
        
        if($next['token']==='repeat') {                        
            $next = vRepeat(nextToken(fgets($GLOBALS['f'])));
        }
         
        return $next;
    }

    function vEnd($next){
        if($next['token']!==';'){
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \';\'');
        }
        $next = nextToken(fgets($GLOBALS['f']));
        if($next['token']!=='end'){
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'end\'');
        }
        $arg = explode(' | ', trim(fgets($GLOBALS['f'])));
        if($arg[0] ==="") {
            fseek($GLOBALS['f'],-36, SEEK_CUR);
            $arg = explode(' | ', trim(fgets($GLOBALS['f'])));
            erro($arg[3]+1, '4', verifSimboloInesp(['valor'=>'.']), 2 , ' \'end\'');
        }
        
        $next = [
            'token' => $arg[0],
            'lexema' => $arg[1],
            'valor' => $arg[2],
            'lin' => $arg[3],
            'col' => $arg[4]
        ];
        
        if($next['token']!=='.'){            
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'.\'');
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
                //semantico
                varNaoDeclarada($next);
                tipoCompativel($next);
                $next = nextToken(fgets($GLOBALS['f']));
                if(!verifAritmetico($next['token'])&&$next['token'] !== ';'&&$next['token'] !== ')') {
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'(-+*/)\'');
                }
            }

            if(verifAritmetico($next['token'])) {
                tipoCompativel($next);
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
                tipoCompativel($next);
                $next = nextToken(fgets($GLOBALS['f']));
                if($next['token']!==')'&&!verifAritmetico($next['token'])&&$next['token']!==';') {
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'(\' , \')\' ou (+-*/)');
                }
            }

            if($next['token']===')') {
                --$cout;                
                $next = nextToken(fgets($GLOBALS['f']));
                if($next['token']!==')'&&!verifAritmetico($next['token'])&&$next['token']!==';') {                                       
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
        setTipoCompativel(null);
        $next = bloco(nextToken(fgets($GLOBALS['f'])));
        
        return $next;
    }

    function vRepeat($next) {
        do{           
            if($next['token']!=='begin'){                
                erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'begin\'');
             }            
            $next = bloco(nextToken(fgets($GLOBALS['f'])));
        }while($next['token']!=='end');
        $next = nextToken(fgets($GLOBALS['f']));

        if($next['token']!==';'){ erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \';\''); }
        $next = nextToken(fgets($GLOBALS['f']));
        
        if($next['token']!=='until'){ erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'until\''); }
        $next = vExp(nextToken(fgets($GLOBALS['f'])), ';');
       
        return $next;
    }

    function vWhile($next) {       
        $next = vExp($next,'do');

        if($next['token']!=='end'){
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'end\'');        
        }
        
        $next = nextToken(fgets($GLOBALS['f']));
        if($next['token']!==';'){ erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \';\''); }
               
        return bloco(nextToken(fgets($GLOBALS['f'])));
    }
    function vIf($next)
    {        
        $next = vExp($next,'then');
        if($next['token']!=='end'&&$next['token']!=='else'){
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'comando\'');
        }
        
        if($next['token']==='else') {
            $next = vElse(nextToken(fgets($GLOBALS['f'])));
        }

        if($next['token']==='end') {
            $next = nextToken(fgets($GLOBALS['f']));        
            if($next['token']!==';'){ erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \';\''); }            
        }

        return bloco(nextToken(fgets($GLOBALS['f'])));
    }

    function vElse($next)
    {
        if($next['token']!=='begin'){
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'begin\'');
        }            
        $next = nextToken(fgets($GLOBALS['f']));

        $next = bloco($next);
        if($next['token']!=='end'){
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'end\'');        
        }
        return $next;        

        // $next = nextToken(fgets($GLOBALS['f']));
        // if($next['token']!==';'){ erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \';\''); }
        // // dd($next);
        // return nextToken(fgets($GLOBALS['f']));
    }

    function vExp($next, $PARA='then') {
        $cout = 0;    
        $ari = false;
        $rel = false;
        $dent = false;

        $bol = false;
        
        if($next['token']!=='(') {
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'(\'');
        }
        $next = nextToken(fgets($GLOBALS['f']));
        
        if($next['token']!=='id'&&$next['token']!=='('&&$next['token']!=='numerico') {
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'id\' ou \'numerico\'');
        }

        while($next['token']!==$PARA) {
            if($next['token']==='id'){
                //semantico
                varNaoDeclarada($next);
                tipoCompativelExp($next);
                $next = nextToken(fgets($GLOBALS['f']));
                if(!verifRelacional($next['token'])&&$next['token'] !== ')'&&!verifAritmetico($next['token'])) {
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \')\' ou \'(relacional)\'');
                }
                if((verifRelacional($next['token'])&&$rel)||(verifAritmetico($next['token'])&&$ari)){
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \')\' ou \'(expressão valida)\'');
                }                
            }

            if(verifRelacional($next['token'])) {                
                //semantico
                tipoCompativelExp($next);
                $rel = true;
                $next = nextToken(fgets($GLOBALS['f']));
                if($next['token']!=='id' &&$next['token']!=='numerico'&&$next['token']!=='(') {
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'id\' ou \'numerico\'');
                }
            }

            if(verifBooleano($next['token'])) {
                //semantico
                // tipoCompativelExp($next);
                $bol = true;
                $next = nextToken(fgets($GLOBALS['f']));
                if($next['token']!=='(') {
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , '  \'(\'');
                }
            }

            if(verifAritmetico($next['token'])) {
                tipoCompativelExp($next);
                $ari = true;
                if($bol){
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'11(expressão valida)\'');
                }
                //semantico
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
                if($next['token']!=='(') setExpCompativel($next);
            }
                      
            if($next['token']==='numerico'){
                //semantico
                tipoCompativelExp($next);
                $next = nextToken(fgets($GLOBALS['f']));
                if($next['token']!==')'&&!verifRelacional($next['token'])&&!verifAritmetico($next['token'])) {
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \')\' , \'(relacional)\'');
                }
                if((verifRelacional($next['token'])&&($rel||$ari)&&$dent)||(verifAritmetico($next['token'])&&$rel&&$dent&&$ari)){                  
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
                if(verifAritmetico($next['token'])&&!$rel){
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
        
        //semantico
        set();

        return bloco(nextToken(fgets($GLOBALS['f'])));
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

    function vDeclaracaoVariaveis($next, $tipo) {
        while($next['token'] !== ';'){
            if($next['token'] !== "id" && $next['token'] !== ','){
                erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'begin\' ou \'(string|real|inteiro)\' ');
            }
            if($next['token'] === "id"){               
                vDecVariavel($next, $tipo);//semantico
                
                $next = nextToken(fgets($GLOBALS['f']));
                if($next['token'] !== ','&&$next['token'] !== ';'){
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \',\' ou \';\' ');
                }
            }
            if($next['token'] === ","){
                $next = nextToken(fgets($GLOBALS['f']));
                if($next['token'] !== 'id'){
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'id\' ');
                }
            }           
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

    function vAll($next)
    {
        if($next['token']==='('){
            while($next['token']!==';'){
                $next = nextToken(fgets($GLOBALS['f']));                
                if($next['token']!=='id'&&$next['token']!==','&&$next['token']!==')'&&$next['token']!==';'){
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \')\', \',\' ou \'id\'');
                }
                if($next['token']==='id'){
                    //semantico
                    varNaoDeclarada($next);
                    tipoCompativelAll($next);
                    $next = nextToken(fgets($GLOBALS['f']));
                    if($next['token']!==','&&$next['token']!==')'){
                        erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \')\' ou \',\'');
                    }
                }  

                if($next['token']===','){
                    $next = nextToken(fgets($GLOBALS['f']));
                    //semantico
                    if($next['token']!=='id'){
                        erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'id\'');
                    }
                    varNaoDeclarada($next);
                    tipoCompativelAll($next);
                }   
            }
        }else{
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'(\'');
        }
        return bloco(nextToken(fgets($GLOBALS['f'])));
    }