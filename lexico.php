

<?php

  $f = $_FILES['arq']['tmp_name'];
  // $file = file($f);
  $file = fopen($f,"r");

  // foreach ($file as $key => $linha) {
  //   var_dump("linha=".$key."------".$linha);
  // }
  while($linha = fgets($file,)){
    var_dump($linha);
    echo "<br>";
    // dd();
  }

  fclose($file);

  function dd(...$var)
  {
    var_dump($var);
    die;
  }
?>