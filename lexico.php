<?php
  $tabela = array();
  $f = $_FILES['arq']['tmp_name'];
  $file = fopen($f,"r");
  $fout = fopen('./tabelas/lexica/tabela.txt','w');
  $linhaCont = 0;
  while($linha = fgets($file)){
    $ete = ord($linha);
    if($linha){
      $tamanhoLinha = strlen($linha);
      $token = null;
      $linhaCont++;
      for ($col=0; $col < $tamanhoLinha ; $col++) {

        if(comparaEte($linha[$col])){
          while(($col<$tamanhoLinha)&&comparaEte($linha[$col])){
            $col++;
          }           
        } 
        
        if($col<$tamanhoLinha){
          verifGeral($linha[$col]);        
          if($linha[$col]=='{') while('}' !== $linha[$col]) $col++;
          
          if(ord($linha[$col])!==13 && $linha[$col]!==' '){
            if($token === null) $colToken = $col;
            $token .= $linha[$col];
            
            if(proxCarac($token)&& (($col+1)<$tamanhoLinha) && !(comparaEte($linha[$col+1]))){
              $concat = $token . $linha[$col+1];
              
              if(proxConcatCarac($linha[$col+1]) && ((verifRelacional($concat) || verifAtribuicao($concat)))){ 
                $token .= $linha[++$col];
                pushTabela($token,$linhaCont,$colToken);
                $token = null;
              }else{
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

        if($token!==null){       
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
                erro($linhaCont, $col+1,$linha[$col+1]);
              }else{            
                pushTabela('ID',$linhaCont,$colToken,$token,null);
                $token=null;
              }
            }
          }else{
            
            if(verifNumerico($token)) {             
              while(!(ord($linha[$col+1])===13 || ord($linha[$col+1])===10)&&($linha[$col+1]==='.'||verifNumerico($linha[$col+1]))){
                verifGeral($linha[$col+1]);
                $token.= $linha[++$col];
              }
              
              if(($linha[$col]==='.'&& !verifNumerico($linha[$col+1]))||$linha[$col+1]===',') erro($linhaCont, $col+1, $linha[$col+1]);

              if(!verifAlfabeto($linha[$col+1])||$linha[$col+1]===';'||$linha[$col+1]===')'||verifAritmetico($linha[$col+1])||$token==='.'){                
                pushTabela('NUMERICO',$linhaCont,$colToken,null,$token);
                $token=null;  
              }else{
                
                erro($linhaCont,$col+1, $token);
              }
            }
          }        
        }      
      }  
    }
  }
  
  fclose($file);
  fclose($fout);
  // gravar2($tabela);
  // dd($tabela);

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


  function pushTabela(string $token, int $lin, int $col, string $lexema=null,  string $valor=null):void
  {           
    $linha = strtolower($token).' | '.strtolower($lexema).' | '.$valor.' | '.$lin.' | '.($col+1)."\n";
    fwrite($GLOBALS['fout'],  $linha);
  }

  // function pushTabela(string $token, int $lin, int $col, string $lexema=null,  string $valor=null):void
  // {    
  //   array_push($GLOBALS['tabela'], [
  //     'token' => $token,
  //     'lexema' => $lexema,      
  //     'valor' => $valor, 
  //     'lin' => $lin,
  //     'col' => ($col + 1)
  //   ]);
  // }

  function gravar(array $tabela)
  {
    $f = fopen('./tabelas/lexica/tabela.txt','w');
    fwrite($f,  print_r($tabela, TRUE));
    fclose($f);
  }

  function gravar2(array $tabela)
  {
    $f = fopen('./tabelas/lexica/tabela.txt','w');
    foreach ($tabela as $tb) {
      $linha = strtolower($tb['token']).' | '.$tb['lexema'].' | '.$tb['valor'].' | '.$tb['lin'].' | '.$tb['col']."\n";
      fwrite($f,  $linha);
    }
    fclose($f);
  }
  