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

    while($linha = fgets($GLOBALS['f'])) {
        if($token===null) {
            $token = nextToken($linha);
            $counToken++;
        }
        $counToken++;
        //if($counToken=== 3) dd($next );
        if($counToken===2) {
            $next = nextToken(fgets($GLOBALS['f']));
            $next = vPrograma($token, $next);
        }else{
            $next = nextToken($linha);
        }
        
        while(eTipo($next['token'])) {
           $next = vDeclaracaoVariaveis(nextToken(fgets($GLOBALS['f'])));
           
           if($next['token']==='begin') {
               $next = vBegin($next);
            //    do{
                   $next = bloco($next);     
                //    $a = fseek($GLOBALS['f'], -36, SEEK_CUR);             
                //    $next = fseek($GLOBALS['f'], -100,SEEK_CUR);         
                               
                   dd($next);
                //    dd(nextToken(fgets($GLOBALS['f'])));
            //    }while($next!=='.');
           }
        }
        dd('NÂO QUEBROU');
        //while
            //if
                //c:=4;
            //end
            //if
                //repeat
                //begin
                    //k:=3
                    //if
                    //end
                //end
                //until(exp)
            //end
        //end
        
        
        
        // if($next['token']==='if') {
        //     $next = vIf(nextToken(fgets($GLOBALS['f'])));
            // while($next!=='end') {
            //     if($next['token']==='else') {
            //         // $next = vElse(nextToken(fgets($GLOBALS['f'])));
            //     }
            // }
            // dd($next);
        // }
        // if($next['token']==='while'){
        //     $next = vWhile($next);
        // }
        
    }
    $bl = array();
     $co = 0;
    function bloco($next)
    {
        if($next['token']==='if'){
            // var_dump($next);        
            $next = vIf(nextToken(fgets($GLOBALS['f'])));
            // dd($next);
        }
        if($next['token']==='while'){          
            $next = vWhile(nextToken(fgets($GLOBALS['f'])));
        }
        if($next['token']==='id') {
            $next = vAtribuicao(nextToken(fgets($GLOBALS['f']))); 
            // if($next['token']==='end'&&$next['lin']!=='29'&&$next['lin']!=='31'&&$next['lin']!=='34'&&$next['lin']!=='37'&&$next['lin']!=='36'&&$next['lin']!=='39'&&$next['lin']!=='39') dd($next,2);         
            // dd($next);
        }
        if($next['token']==='all') {            
            $next = vAll(nextToken(fgets($GLOBALS['f'])));
            // dd($next);
        }
        if($next['token']==='else') {                        
            $next = vElse(nextToken(fgets($GLOBALS['f'])));
            // dd($next);
        }
        
        if($next['token']==='repeat') {            
            // dd($GLOBALS['bl']);
            $next = vRepeat(nextToken(fgets($GLOBALS['f'])));
        }
        // dd($next);   
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
        // dd($next);        
        return $next;
    }

    function vWhile($next) {
        // do{
            $next = vExp($next,'do');

            if($next['token']!=='end'){
                erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'end\'');        
            }
        
        $next = nextToken(fgets($GLOBALS['f']));
        if($next['token']!==';'){ erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \';\''); }
        // }while($next['token']!=='end');
        
        return bloco(nextToken(fgets($GLOBALS['f'])));
    }
    function vIf($next)
    {        
        $next = vExp($next,'then');
        if($next['token']!=='end'&&$next['token']!=='else'){
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'comando\'');            
        }
        
        if($next['token']==='else') {            
            return bloco($next);
        }     
        // dd();   
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
        $i=0;
        // do{
            $next = bloco($next);
            // ++$i;
            
            // dd($next);
        // }while($next['token']!=='end');
        if($next['token']!=='end'){
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'end\'');        
        }
        return $next; 
        // dd($next);
        // dd($next);

        $next = nextToken(fgets($GLOBALS['f']));
        if($next['token']!==';'){ erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \';\''); }
        // dd($next);
        return nextToken(fgets($GLOBALS['f']));
    }
    function vExp($next, $PARA='then') {
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

        //dd($next);
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

    function vAll($next)
    {
        if($next['token']==='('){
            while($next['token']!==';'){
                $next = nextToken(fgets($GLOBALS['f']));                
                if($next['token']!=='id'&&$next['token']!==','&&$next['token']!==')'&&$next['token']!==';'){
                    erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \')\', \',\' ou \'id\'');
                }
                if($next['token']==='id'){
                    $next = nextToken(fgets($GLOBALS['f']));
                    if($next['token']!==','&&$next['token']!==')'){
                        erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \')\' ou \',\'');
                    }
                }  

                if($next['token']===','){
                    $next = nextToken(fgets($GLOBALS['f']));
                    if($next['token']!=='id'){
                        erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'id\'');
                    }
                }   
            }
        }else{
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , ' \'(\'');
        }
        return bloco(nextToken(fgets($GLOBALS['f'])));
    }