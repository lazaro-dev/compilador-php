<?php    
  
  $t = fopen('./tabelas/lexica/intermediario.txt','r');
  $o = fopen('./tabelas/lexica/objeto.txt','w');
  // $temp = 1;
  
  
  $z ='COPY ';
  $y ='ADD ';
  $x ='MUL ';
  $w ='DIV ';
  $k ='SUB ';
  $p ='STORE ';
  
  
  while($linha = fgets($GLOBALS['t'])){
    // dd($linha);
    $ten = explode(':=', $linha);
    
    if(count($ten)===2){

      $te = explode("*", trim($ten[1]));
      if(count($te)>1){
        $jo = explode(' ', trim($ten[0]));
        if(count($jo)>1){
          // var_dump($jo);
          $z1 = $jo[0].' '.$z.' ';
          $atr = $jo[1].' ';
        }else{
          $z1 = $z;
          $atr = $ten[0];
        }
        $load1 = $z1.$te[0].', R0';
        $load2 = $z1.$te[1].', R1';
        $mul = $x.'R1, R0';
        $sto = $p.'R0, '.$atr;
        fwrite($GLOBALS['o'],  $load1." \n");
        fwrite($GLOBALS['o'],  $load2." \n");
        fwrite($GLOBALS['o'],  $mul." \n");
        fwrite($GLOBALS['o'],  $sto." \n");
      }else{
        $te = null;
        $te = explode('/', trim($ten[1]));      
        if(count($te)>1){
          $jo = explode(' ', trim($ten[0]));
          if(count($jo)>1){
            // var_dump($jo);
            $z1 = $jo[0].' '.$z.' ';
            $atr = $jo[1].' ';
          }else{
            $z1 = $z;
            $atr = $ten[0];
          }
          $load1 = $z1.$te[0].', R0';
          $load2 = $z1.$te[1].', R1';
          $div = $w.'R1, R0';
          $sto = $p.'R0, '.$atr;
          fwrite($GLOBALS['o'],  $load1." \n");
          fwrite($GLOBALS['o'],  $load2." \n");
          fwrite($GLOBALS['o'],  $mul." \n");
          fwrite($GLOBALS['o'],  $sto." \n");
        }else{
          $te = null;
          $te = explode("+", trim($ten[1]));      
          if(count($te)>1){
            $jo = explode(' ', trim($ten[0]));
            if(count($jo)>1){
              // var_dump($jo);
              $z1 = $jo[0].' '.$z.' ';
              $atr = $jo[1].' ';
            }else{
              $z1 = $z;
              $atr = $ten[0];
            }
            $load1 = $z1.$te[0].', R0';
            $load2 = $z1.$te[1].', R1';
            $add = $y.'R1, R0';
            $sto = $p.'R0, '.$atr;
            fwrite($GLOBALS['o'],  $load1." \n");
            fwrite($GLOBALS['o'],  $load2." \n");
            fwrite($GLOBALS['o'],  $mul." \n");
            fwrite($GLOBALS['o'],  $sto." \n");
          }else{
            $te = null;
            $te = explode('-', trim($ten[1]));      
            if(count($te)>1){
              $jo = explode(' ', trim($ten[0]));
              if(count($jo)>1){
                // var_dump($jo);
                $z1 = $jo[0].' '.$z.' ';
                $atr = $jo[1].' ';
              }else{
                $z1 = $z;
                $atr = $ten[0];
              }
              $load1 = $z1.$te[0].', R0';
              $load2 = $z1.$te[1].', R1';
              $sub = $k.'R1, R0';
              $sto = $p.'R0, '.$atr;
              fwrite($GLOBALS['o'],  $load1." \n");
              fwrite($GLOBALS['o'],  $load2." \n");
              fwrite($GLOBALS['o'],  $mul." \n");
              fwrite($GLOBALS['o'],  $sto." \n");
            }else{
              $te = null;
              // $te = explode(':=', $linha);
              $te = explode("<>", trim($ten[1]));
                if(count($te)>1){
                $jo = explode(' ', trim($ten[0]));
                // var_dump($te);
                if(count($jo)>1){
                  $z1 = $jo[0].' '.$z.' ';
                  $atr = $jo[1].' ';
                }else{
                  $z1 = $z;
                  $atr = $ten[0];
                }
                $load1 = $z1.$te[1].' '.$atr;
                $load2 = "<> ".$te[0].''.$atr;
                $load3 = $z1.'NOT '.$atr;
                $load4 = 'CMP '.$atr;
                
                fwrite($GLOBALS['o'],  trim($load1)." \n");
                fwrite($GLOBALS['o'],  trim($load2)." \n");
                fwrite($GLOBALS['o'],  trim($load3)." \n");
                fwrite($GLOBALS['o'],  trim($load4)." \n");
                $na = fgets($GLOBALS['t']);
                $na = explode('goto', $na);
                fwrite($GLOBALS['o'],  trim('JNZ'.$na[count($na)-1])." \n");
              }else{
                $te = null;
                $te = explode(">", trim($ten[1]));
                if(count($te)>1){
                  $jo = explode(' ', trim($ten[0]));
                  // var_dump($te);
                  if(count($jo)>1){
                    $z1 = $jo[0].' '.$z.' ';
                    $atr = $jo[1].' ';
                  }else{
                    $z1 = $z;
                    $atr = $ten[0];
                  }
                  $load1 = $z1.$te[1].' '.$atr;
                  $load2 = "> ".$te[0].''.$atr;
                  $load3 = $z1.'NOT '.$atr;
                  $load4 = 'CMP '.$atr;
                  
                  fwrite($GLOBALS['o'],  trim($load1)." \n");
                  fwrite($GLOBALS['o'],  trim($load2)." \n");
                  fwrite($GLOBALS['o'],  trim($load3)." \n");
                  fwrite($GLOBALS['o'],  trim($load4)." \n");
                  $na = fgets($GLOBALS['t']);
                  $na = explode('goto', $na);
                  fwrite($GLOBALS['o'],  trim('JNZ'.$na[count($na)-1])." \n");
                  }else{
                    $te = null;
                    $te = explode("<", trim($ten[1]));
                    if(count($te)>1){
                      $jo = explode(' ', trim($ten[0]));
                      // var_dump($te);
                      if(count($jo)>1){
                        $z1 = $jo[0].' '.$z.' ';
                        $atr = $jo[1].' ';
                      }else{
                        $z1 = $z;
                        $atr = $ten[0];
                      }
                      $load1 = $z1.$te[1].' '.$atr;
                      $load2 = "< ".$te[0].''.$atr;
                      $load3 = $z1.'NOT '.$atr;
                      $load4 = 'CMP '.$atr;
                      
                      fwrite($GLOBALS['o'],  trim($load1)." \n");
                      fwrite($GLOBALS['o'],  trim($load2)." \n");
                      fwrite($GLOBALS['o'],  trim($load3)." \n");
                      fwrite($GLOBALS['o'],  trim($load4)." \n");
                      $na = fgets($GLOBALS['t']);
                      $na = explode('goto', $na);
                      fwrite($GLOBALS['o'],  trim('JNZ'.$na[count($na)-1])." \n");
                      }else{
                        $te = null;
                        $te = explode("<=", trim($ten[1]));
                        if(count($te)>1){
                          $jo = explode(' ', trim($ten[0]));
                          // var_dump($te);
                          if(count($jo)>1){
                            $z1 = $jo[0].' '.$z.' ';
                            $atr = $jo[1].' ';
                          }else{
                            $z1 = $z;
                            $atr = $ten[0];
                          }
                          $load1 = $z1.$te[1].' '.$atr;
                          $load2 = "< ".$te[0].''.$atr;
                          $load3 = $z1.'NOT '.$atr;
                          $load4 = 'CMP '.$atr;
                          
                          fwrite($GLOBALS['o'],  trim($load1)." \n");
                          fwrite($GLOBALS['o'],  trim($load2)." \n");
                          fwrite($GLOBALS['o'],  trim($load3)." \n");
                          fwrite($GLOBALS['o'],  trim($load4)." \n");
                          $na = fgets($GLOBALS['t']);
                          $na = explode('goto', $na);
                          fwrite($GLOBALS['o'],  trim('JNZ'.$na[count($na)-1])." \n");
                          }else{
                            $te = null;
                            $te = explode(">=", trim($ten[1]));
                            if(count($te)>1){
                              $jo = explode(' ', trim($ten[0]));
                              // var_dump($te);
                              if(count($jo)>1){
                                $z1 = $jo[0].' '.$z.' ';
                                $atr = $jo[1].' ';
                              }else{
                                $z1 = $z;
                                $atr = $ten[0];
                              }
                              $load1 = $z1.$te[1].' '.$atr;
                              $load2 = "< ".$te[0].''.$atr;
                              $load3 = $z1.'NOT '.$atr;
                              $load4 = 'CMP '.$atr;
                              
                              fwrite($GLOBALS['o'],  trim($load1)." \n");
                              fwrite($GLOBALS['o'],  trim($load2)." \n");
                              fwrite($GLOBALS['o'],  trim($load3)." \n");
                              fwrite($GLOBALS['o'],  trim($load4)." \n");
                              $na = fgets($GLOBALS['t']);
                              $na = explode('goto', $na);
                              fwrite($GLOBALS['o'],  trim('JNZ'.$na[count($na)-1])." \n");
                              }else{
                                $te = null;
                                // $load1 = $z.trim($ten[1]).', '.trim($ten[0]);
                                // fwrite($GLOBALS['o'],  $load1." \n");
                              }
                          }
                      }
                  }
              }











            }
          }
        }
      }



      

    }else{
      $ten = $ten[0];

    }



  }


dd(1);



  // function setTres($l) {
    
  //   $linha = $l."\n";
  //   fwrite($GLOBALS['ft'],  $linha);
  //   $GLOBALS['linTres'] = null;
  // }
  
  // function setArrLab($lb=null, $n=null)
  // {    
  //   if($lb!==null) $lb = trim($lb).' ';
  //   $it= $lb."#tmp".$GLOBALS['temp']." := ".trim($GLOBALS['linTres']);
  //   $it1 = "if not #tmp".$GLOBALS['temp']." goto LABEL".(($n!==null)?$n:$GLOBALS['label']);
  //   $GLOBALS['temp']++;
  //   $GLOBALS['label']++;
  //   setTres($it);
  //   setTres($it1);
  // }





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