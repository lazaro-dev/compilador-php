<?php    

  include 'arvore.class.php';
  include 'no.class.php';
  
  $ft = fopen('./tabelas/lexica/intermediario.txt','w');
  $temp = 0;
  function setTres($l) {
    // dd('aa');
    $GLOBALS['label']++;
    $linha = $l."\n";
    fwrite($GLOBALS['ft'],  $linha);
    $GLOBALS['linTres'] = null;
  }
  
  function setArrLab()
  {
    $h = explode('and',$GLOBALS['linTres']);
    if(count($h)===0){
      $h = explode('or',$GLOBALS['linTres']);
    }
    $h[0] = '#tmp'.$GLOBALS['temp'].':=';
    $GLOBALS['temp']++;
    dd($GLOBALS['linTres']);
    
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