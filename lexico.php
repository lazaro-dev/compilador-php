<?php
  $tabela = array();
  $f = $_FILES['arq']['tmp_name'];
  $file = fopen($f,"r");

  $linhaCont = 0;
  while($linha = fgets($file)){
    if($linha){
      $linha = trim($linha);
      $tamanhoLinha = strlen($linha);
      $token = null;
      $linhaCont++;
      for ($col=0; $col < $tamanhoLinha ; $col++) {       
        verifGeral($linha[$col]);
        if($linha[$col]=='{') while('}' !== $linha[$col]) $col++;
        
        if($linha[$col]!=="\n" && $linha[$col]!==' '){
          if($token === null) $colToken = $col;
          $token .= $linha[$col];
          
          if(proxCarac($token)&& (($col+1)<$tamanhoLinha) && (!$linha[$col+1]!=='\n')){          
            $concat = $token . $linha[$col+1];
            
            if(proxConcatCarac($linha[$col+1]) && ((verifRelacional($concat) || verifAtribuicao($concat)))){            
              $token .= $linha[++$col];
              pushTabela($token,$linhaCont,$colToken);
              $token = null;
            }else{
              //erro             
              if($linha[$col+1]===' '&&$linha[$col]===':') erro($linhaCont, $col+1);
            }
          }
          
          if( $token!==null && (verifAritmetico($token) || verifEspeciais($token) ||  verifRelacional($token))){        
            $token = $linha[$col];
            pushTabela($token,$linhaCont,$colToken);
            $token = null;
          }
        }else{
          $token = null;        
        }
        
        if($token!==null){          
          if(verifAlfabeto($token)){                    
            while(((($col+1)<$tamanhoLinha)&&$linha[$col+1]!=="\n")&&((verifAlfabeto($linha[$col+1]) || (verifNumerico($linha[$col+1]))))){
              verifGeral($linha[$col+1]);
              $token.= $linha[++$col];
            }
            
            if(verifPalavReser($token)){          
              pushTabela($token,$linhaCont,$colToken);
              $token = null;
            }else{            
              if((($col+1)<$tamanhoLinha)&&$linha[$col+1]!==' '&&!verifProxVariavel($linha[$col+1])){
                erro($linhaCont, $col+1);
              }else{            
                pushTabela('ID',$linhaCont,$colToken,$token,null);
                $token=null;            
              }          
            }
          }else{
            
            if(verifNumerico($token)) {             
              while((!$linha[$col+1]!=="\n")&&($linha[$col+1]==='.'||verifNumerico($linha[$col+1]))){
                verifGeral($linha[$col+1]);
                $token.= $linha[++$col];
              }
              if(!verifAlfabeto($linha[$col+1])||$linha[$col+1]===';'||$linha[$col+1]===')'||verifAritmetico($linha[$col+1])||$token==='.'){
                
                pushTabela('NUMERICO',$linhaCont,$colToken,null,$token);
                $token=null;  
              }else{
                erro($linhaCont, $col+1);
              }
            }
          }        
        }      
      }  
    }  
  }
  
  fclose($file);
  dd($tabela);

  function verifProxVariavel(string $c){
    $regra = '/^(<|>|:|=|\)|\,|\;)$/';
    return (
      preg_match($regra, $c) || verifAritmetico($c) || 
      verifAlfabeto($c) || verifNumerico($c)
    );
  }

  function pushTabela(string $token, int $lin, int $col, string $lexema=null,  string $valor=null):void
  {    
    array_push($GLOBALS['tabela'], [
      'token' => $token,
      'lexema' => $lexema,      
      'valor' => $valor, 
      'lin' => $lin,
      'col' => ($col + 1)
    ]);
  }
  function proxCarac(string $c):bool
  {
    $regra = '/^(<|>|:|\.)$/';
    return preg_match($regra, $c);
  }

  function proxConcatCarac(string $c):bool
  {
    $regra = '/^(>|=)$/';
    return preg_match($regra, $c);    
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

  function verifGeral(string $c):void
  {
    if(valida($c)) erro($GLOBALS['linhaCont'], $GLOBALS['col']);
  }

  function valida(string $c):bool
  {
    return (
      !verifRelacional($c)&&!verifAlfabeto($c)&&
      !verifNumerico($c)&&!verifEspeciais($c)&&
      !verifAtribuicao($c)&&!verifComentario($c)&&
      !verifAritmetico($c)&&$c!==' '
    );
  }

  function erro(int $lin, int $col, string $codigo = '500',string $err = 'TOKEN Invalido'):void
  {
    dd('Erro '.$codigo.' na linha '.$lin.':'.$col.' '.$err);
  }

  function dd(...$var)
  {
    var_dump($var);
    die;
  }

  
  $palavraReservada = array('programa','begin', 'end','if','then','else','while',
    'do','until','repeat','integer','real','all','and','or','string'); # '/(programa | begin | end | if | then | else | while | do | until | repeat | string | integer | real | all | and | or)/'

    //  expReg: '/^[a-z]{1,10}$/i'   '/^[a-z]+$/i'

  $aritmetico = array('+', '-', '*', '/'); # '/( + | - | * | \/ )/'

  $booleanos = array('or', 'and'); # '/( or | and)/'

  $relacional = array('<','>','<=','>=','=','<>'); # '/(< | > | <= | >= | = | <>)/' 
  
  $comentario = array('{','}'); # '/( { | } )/' 

  $especiais = array('(', ')', ';', ',', '.', ':', '='); # '/( \( | \) | ; | , | . | : | =)/'

  $atribuicao = array(':', '=', ':='); # '/( : | = | := )/'

  $alfabeto = array('a','b','c','d','e','f','g','h','i','j','l','m'
    ,'n','o','p','q','r','s','t','u','v','x','z','w','y','k'); # '/(^[a-z]+$)/'

  $numerico = array('0','1','2','3','4','5','6','7','8','9'); # #inteiro# '/(^[0-9]+$)/'  #real# '/(^[0-9]+.[0-9]{1,5}$)/'