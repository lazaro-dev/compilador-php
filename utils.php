<?php
    //GERAL
    function erro(int $lin, int $col,string $tokenInv ,string $codigo = '1',string $err = 'TOKEN Invalido'):void
    {
        if($codigo === '1') dd('Erro '.$codigo.': na linha '.$lin.':'.$col.' '.$err.' \''.$tokenInv.'\' é extraído da tabela de erros.');
        
        if($codigo === '2') dd('Erro '.$codigo.':  Símbolo '."'".$tokenInv."'".' inesperado. Esperando '.$err.' Linha '.$lin.', Coluna '.$col);

        if($codigo === '3') dd('Erro '.$codigo.': Tipos incompatíveis. '."'".$err."'".' e '."'".$tokenInv."'".'  Linha '.$lin.', Coluna '.$col);

        if($codigo === '4' || $codigo === '5') dd('Erro '.$codigo.': '.$err.'  Linha '.$lin.', Coluna '.$col);
    
    }
    
    function dd(...$var)
    {
        var_dump($var);
        die;
    }

    function nextToken($linha)
    {
        $arg = explode(' | ', trim($linha));
        if($arg[0] ==="") {
            fseek($GLOBALS['f'],-36, SEEK_CUR);
            $arg = explode(' | ', trim(fgets($GLOBALS['f'])));
            erro($arg[3]+1, '1', verifSimboloInesp(['valor'=>'end']), 2 , ' \'end\'');
        }
        return [
            'token' => $arg[0],
            'lexema' => $arg[1],
            'valor' => $arg[2],
            'lin' => $arg[3],
            'col' => $arg[4]
        ];
    }


    function verifSimboloInesp($token)
    {
        if($token['valor']!=="") return $token['valor'];
        if($token['lexema']!=="") return $token['lexema'];
        if($token['token']!=="") return $token['token'];
    }      
    //-------------------------------------------------------------------------------------------------------------------------------------

    function verifPalavReser(string $token):bool
    {
        $regra = '/^(program|begin|end|if|then|else|while|do|until|repeat|string|integer|real|all|and|or)$/i';
        return preg_match($regra, $token);
    }

    function verifAritmetico(string $token):bool
    {
        $regra = '/^(\+|\-|\*|\/)$/';
        return preg_match($regra, $token);
    }
    function verifBooleano(string $token):bool
    {
        $regra = '/^(or|and)$/';
        return preg_match($regra, $token);
    }
    function verifRelacional(string $token):bool
    {
        $regra = '/^(<|>|<=|>=|=|<>)$/';
        return preg_match($regra, $token);
    }
    function verifComentario(string $token):bool
    {
        $regra = '/^({|})$/';
        return preg_match($regra, $token);
    }
    function verifEspeciais(string $token):bool
    {
        $regra = '/^\(|\)|\;|\,|\.|\:|\=$/';
        return preg_match($regra, $token);
    }
    function verifAtribuicao(string $token):bool
    {
        $regra = '/^(:|=|:=)$/';
        return preg_match($regra, $token);
    }
    function verifAlfabeto(string $token):bool
    {
        $regra = '/^[a-z]+$/i';
        return preg_match($regra, $token);
    }

    function verifNumerico(string $token):bool
    {
        $regra = '/^[0-9]$/';
        return preg_match($regra, $token);
    }

  //Sintatico

  function eTipo(string $token):bool
  {
      $regra = '/^(string|integer|real)$/i';
      return preg_match($regra, $token);
  }