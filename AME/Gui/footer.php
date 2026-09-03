<?php

echo 
" 
</div>
</div>
</div>

        <div class=\"loading-modal\" style=\"background: rgba( 51, 51, 51, 0.0 ) url(".URL_ROOT."img/Spin.png) 50% 50% no-repeat;\">
        </div>

            <footer class=\"footer\">
                                   
            </footer>";

                $loadjs = new Loads();
                $loadjs->js();

        echo "      
            <!-- Modal -->
                <div class=\"modal fade\" id=\"modal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"exampleModalLabel\" aria-hidden=\"true\">
                  <div class=\"modal-dialog\" role=\"document\">
                    <div class=\"modal-content\">
                      <div class=\"modal-header\">
                        <h5 class=\"modal-title\" id=\"ModalLabel\">Datas</h5>
                        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Fechar\">
                          <span aria-hidden=\"true\">&times;</span>
                        </button>
                      </div>
                      <div name=\"\" id=\"\" class=\"modal-body\">
                            <table class=\"\" id=\"datas\" name=\"datas\">
                            </table>
                      </div>
                      <div class=\"modal-footer\">
                        <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Fechar</button>
                        <button type=\"button\" id=\"saveSelectedDatas\" class=\"btn btn-primary\">Salvar mudanças</button>
                      </div>
                    </div>
                  </div>
                </div>";
           // include 'chat.php';
    echo "</div>                       
    </body> 
</html>";