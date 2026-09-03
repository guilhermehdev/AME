<?php

if($this->getData('dados')) {
    $dados = $this->getData('dados');
} 

Daoagendas::getVagasUBS($dados[0], $dados[1], $dados[2], $dados[3]);