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
    while($linha = fgets($GLOBALS['f'])){
        
        if($token===null){
            $token = nextToken($linha);
        }
        // dd($token);
        // $token = new stdClass();
        // $token->token = $arg[0];
        // $token->lin = $arg[3];
        // $token->col = $arg[4];
        // dd($token);
        $next = nextToken(fgets($GLOBALS['f']));
        if(($token['token'] === 'programa') && ($next['token'] !== 'id')) {
            // dd($next);
            erro($next['lin'], $next['col'], verifSimboloInesp($next), 2 , 'esperando \'ID\'');
        }else{

        }
    }
 //aribuição na declaração da variavel
    