<?php

class Arvore {
        
    public function inserir($node, $val) {
        if ($node != NULL) {

            if (verifAritmetico($node->val)) {
                if ($node->esq != NULL && $node->dir!= NULL) {
                    $this->inserir($node->esq, $val);
                } else {
                    if($node->esq == NULL){
                        $node->esq = new No($val);
                    }else{
                        $this->inserir($node->dir, $val);
                    }
                }
            } else if (verifBooleano($node->val)) {
                if ($node->dir != NULL) {
                    $this->inserir($node->dir, $val);
                } else {
                    $node->dir = new No($val);
                }
            }
        }else{
            $node = new No($val);
        }

    }

    function em_ordem($no) {
        if($no != null){
            $this->em_ordem($no->esq);
            echo $no->val . " ";
            $this->em_ordem($no->dir);
        }
    }
}
