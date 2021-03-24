<?php
  $tabela = array();
  $f = $_FILES['arq']['tmp_name'];
  $file = fopen($f,"r");

  $linhaCont = 0;
  while($linha = fgets($file)){
    $ete = ord($linha);
    
    // if($ete === 13 || $ete === 9 || $ete === 32) $linhaCont++;
    // dd($linha[0]);
    //!($ete === 13) || !($ete === 9) || !($ete === 32)
    if($linha){
      // $linha = trim($linha);
      $tamanhoLinha = strlen($linha);
      $token = null;
      $linhaCont++;
      for ($col=0; $col < $tamanhoLinha ; $col++) {

        if(comparaEte($linha[$col])){
          // if() dd();
          while(($col<$tamanhoLinha)&&comparaEte($linha[$col])){
            $col++;
          } 
          // if ( $col===5)dd($linha[$col]);
        } 
        
        if($col<$tamanhoLinha){
          verifGeral($linha[$col]);        
          if($linha[$col]=='{') while('}' !== $linha[$col]) $col++;
          
          if(ord($linha[$col])!==13 && $linha[$col]!==' '){
            // dd(ord($linha[$col]));
            if($token === null) $colToken = $col;
            $token .= $linha[$col];
            
            if(proxCarac($token)&& (($col+1)<$tamanhoLinha) && !(comparaEte($linha[$col+1]))){
              $concat = $token . $linha[$col+1];
              
              if(proxConcatCarac($linha[$col+1]) && ((verifRelacional($concat) || verifAtribuicao($concat)))){ 
                $token .= $linha[++$col];
                pushTabela($token,$linhaCont,$colToken);
                $token = null;
              }else{
                //erro     
                // dd();
                if($linha[$col+1]===' '&&$linha[$col]===':') erro($linhaCont, $col+1, $linha[$col+1]);
              }
            }
            
            
            if( $token!==null && (verifAritmetico($token) || verifEspeciais($token) ||  verifRelacional($token))){        
              $token = $linha[$col];
              pushTabela($token,$linhaCont,$colToken);
              $token = null;
            }
          }
        }else{
          $token = null;        
        }
        // dd($token); 

        if($token!==null){       
          // if($linhaCont===3) dd($linha, $linhaCont, $col);   
          // if($linhaCont===3) dd(comparaEte($linha[$col]));
          if(verifAlfabeto($token)){                    
            while(((($col+1)<$tamanhoLinha)&& !comparaEte($linha[$col+1]))&&((verifAlfabeto($linha[$col+1]) || (verifNumerico($linha[$col+1]))))){
              verifGeral($linha[$col+1]);
              $token.= $linha[++$col];
            }
            
            if(verifPalavReser($token)){
              pushTabela($token,$linhaCont,$colToken);
              $token = null;
            }else{
              if((($col+1)<$tamanhoLinha)&&!comparaEte($linha[$col+1])&&!verifProxVariavel($linha[$col+1])){
                // dd(ord($linha[$col+1]);
                erro($linhaCont, $col+1,$linha[$col+1]);
              }else{            
                pushTabela('ID',$linhaCont,$colToken,$token,null);
                $token=null;
              }
            }
          }else{
            
            if(verifNumerico($token)) {             
              while((!ord($linha[$col+1])===13 || !ord($linha[$col+1])===10)&&($linha[$col+1]==='.'||verifNumerico($linha[$col+1]))){
                verifGeral($linha[$col+1]);
                $token.= $linha[++$col];
              }
              if($linha[$col]==='.'&& !verifNumerico($linha[$col+1])) erro($linhaCont, $col+1, $linha[$col+1]);

              if(!verifAlfabeto($linha[$col+1])||$linha[$col+1]===';'||$linha[$col+1]===')'||verifAritmetico($linha[$col+1])||$token==='.'){                
                pushTabela('NUMERICO',$linhaCont,$colToken,null,$token);
                $token=null;  
              }else{
                // dd();
                erro($linhaCont,$col+1, $token);
              }
            }
          }        
        }      
      }  
    }
  }
  
  fclose($file);
  gravar2($tabela);
  dd($tabela);

  function verifProxVariavel(string $c){
    $regra = '/^(<|>|:|=|\)|\,|\;)$/';
    return (
      preg_match($regra, $c) || verifAritmetico($c) || 
      verifAlfabeto($c) || verifNumerico($c)
    );
  }

  function comparaEte(string $c):bool {    
    return (
      (ord($c)===13)||(ord($c)===10)||(ord($c)===9)||(ord($c)===32)
    );
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
    if(valida($c)){
      // dd(ord($c));
      erro($GLOBALS['linhaCont'], $GLOBALS['col'], $c);
    } 
  }

  function valida(string $c):bool
  {
    return (
      !verifRelacional($c)&&!verifAlfabeto($c)&&
      !verifNumerico($c)&&!verifEspeciais($c)&&
      !verifAtribuicao($c)&&!verifComentario($c)&&
      !verifAritmetico($c)&&$c!==' '&&!(ord($c)===13)&&
      !(ord($c)===10)&&!(ord($c)===9)&&!(ord($c)===32)
    );
  }

  function erro(int $lin, int $col,string $tokenInv ,string $codigo = '1',string $err = 'TOKEN Invalido'):void
  {
    dd('Erro '.$codigo.' na linha '.$lin.':'.$col.' '.$err.' '.$tokenInv);
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

  function gravar(array $tabela)
  {
    $f = fopen('./tabelas/lexica/tabela.txt','w');
    fwrite($f,  print_r($tabela, TRUE));
    fclose($f);
    // dd($tabela);
  }

  function gravar2(array $tabela)
  {
    $f = fopen('./tabelas/lexica/tabela.txt','w');
    foreach ($tabela as $tb) {
      $linha = $tb['token'].' | '.$tb['lexema'].' | '.$tb['valor'].' | '.$tb['lin'].' | '.$tb['col']."\n";
      fwrite($f,  $linha);
    }
    fclose($f);
    // dd($tabela);
  }
  
  $palavraReservada = array('programa','begin', 'end','if','then','else','while',
    'do','until','repeat','integer','real','all','and','or','string'); # '/(programa | begin | end | if | then | else | while | do | until | repeat | string | integer | real | all | and | or)/'

    //  expReg: '/^[a-z]{1,10}$/i'   '/^[a-z]+$/i'

  $aritmetico = array('+', '-', '*', '/'); # '/( + | - | * | \/ )/'

  $booleanos = array('or', 'and'); # '/( or | and)/'

  $relacional = array('<','>','<=','=>','=','<>'); # '/(< | > | <= | >= | = | <>)/' 
  
  $comentario = array('{','}'); # '/( { | } )/' 

  $especiais = array('(', ')', ';', ',', '.', ':', '='); # '/( \( | \) | ; | , | . | : | =)/'

  $atribuicao = array(':', '=', ':='); # '/( : | = | := )/'

  $alfabeto = array('a','b','c','d','e','f','g','h','i','j','l','m'
    ,'n','o','p','q','r','s','t','u','v','x','z','w','y','k'); # '/(^[a-z]+$)/'

  $numerico = array('0','1','2','3','4','5','6','7','8','9'); # #inteiro# '/(^[0-9]+$)/'  #real# '/(^[0-9]+.[0-9]{1,5}$)/'