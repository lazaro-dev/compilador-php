<?php    

  include 'arvore.class.php';
  include 'no.class.php';
  
  $ft = fopen('./tabelas/lexica/intermediario.txt','w');
  $temp = 1;
  function setTres($l) {
    // dd('aa');
    // $GLOBALS['label']++;
    $linha = $l."\n";
    fwrite($GLOBALS['ft'],  $linha);
    $GLOBALS['linTres'] = null;
  }
  
  function setArrLab($lb=null, $n=null)
  {    
    if($lb!==null) $lb = trim($lb).' ';
    $it= $lb."#tmp".$GLOBALS['temp']." := ".trim($GLOBALS['linTres']);
    $it1 = "if not #tmp".$GLOBALS['temp']." goto LABEL".(($n!==null)?$n:$GLOBALS['label']);
    $GLOBALS['temp']++;
    $GLOBALS['label']++;
    setTres($it);
    setTres($it1);
  }





  // $no = new No('0');
  // $arvore = new Arvore();

  // $arvore->inserir($no, 8);
  // $arvore->inserir($no, 6);
  // $arvore->inserir($no, 10);
  // $arvore->inserir($no, 12);

  // $arvore->em_ordem($no);

  

  // $h = explode('and',$GLOBALS['linTres']);
  // if(count($h)===0){
  //   $h = explode('or',$GLOBALS['linTres']);
  // }
  // dd($h);  
  // dd('fim');




  // function setArrLab()
  // {
  //   $itens = explode(' ',trim($GLOBALS['linTres']));
  //   $qtd = count($itens);
  //   $temp = null;
  //   $i = 0;
  //   $op = 0;
  //   $arr =[];
  //   foreach ($itens as $key => $iten) {
  //     if(verifAritmetico($iten) || verifRelacional($iten) || verifBooleano($iten)){
  //       $i++;
  //     }
  //     if($op === 2){
  //       $op = 0;
  //       $temp = null;
  //       continue; 
  //     }
  //     if($op ===0){
  //       if(!array_key_exists('temp1', $arr)){
  //         $arr['temp1'] = 
  //       }
  //       $temp .= 'temp1';
  //     }
  //     $op++;
  //     $temp .= $iten;
  //   }

  //   dd($itens);
  //   $h = explode('and',$GLOBALS['linTres']);
  //   if(count($h)===0){
  //     $h = explode('or',$GLOBALS['linTres']);
  //   }
  //   $h[0] = '#tmp'.$GLOBALS['temp'].':=';
  //   $GLOBALS['temp']++;
  //   // dd($GLOBALS['linTres']);
    
  // }