

<?php

  $f = $_FILES['arq']['tmp_name'];
  // $file = file($f);
  $file = fopen($f,"r");

  while($linha = trim(fgets($file))){
    // var_dump($linha);
    $tamanhoLinha = strlen($linha);
    $token = null;
    for ($col=0; $col < $tamanhoLinha ; $col++) { 
      // if($linha[$col]=='{'){
      //   while()
      // }
      if($linha[$col]!=='\n' && $linha[$col]!==' '){
        $token .= $linha[$col];
        // verificaToken($token);
      }else{
        var_dump(verificaPalavReser($token));
        $token = null;
        dd();
      }
    }
    
  }

  fclose($file);

  function verificaToken(string $token){
    var_dump($token);
    echo "<br>";
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
 ,'n','o','p','q','r','s','t','u','v','x','z','w','y','k'); # '/(^[-|+]?[a-z]+$)/'

$numerico = array('0','1','2','3','4','5','6','7','8','9'); # #inteiro# '/(^[0-9]+$)/'  #real# '/(^[-|+]?[0-9]+.[0-9]{1,5}$)/'

function verificaPalavReser(string $token):bool
{
  $regra = '/^(programa|begin|end|if|then|else|while|do|until|repeat|string |integer|real|all|and|or)$/';   
  return preg_match($regra, $token);
}
?>