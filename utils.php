<?php
    function erro(int $lin, int $col,string $tokenInv ,string $codigo = '1',string $err = 'TOKEN Invalido'):void
    {
        if($codigo === '1') dd('Erro '.$codigo.': na linha '.$lin.':'.$col.' '.$err.' '.$tokenInv.' é extraído da tabela de erros.');
        
        if($codigo === '2') dd('Erro '.$codigo.':  Símbolo '."'".$tokenInv."'".' inesperado. Esperando '.$err.' Linha '.$lin.', coluna '.$col);
    }

    function verifPalavReser(string $token):bool
    {
        $regra = '/^(programa|begin|end|if|then|else|while|do|until|repeat|string|integer|real|all|and|or)$/i';
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

    function dd(...$var)
    {
        var_dump($var);
        die;
    }