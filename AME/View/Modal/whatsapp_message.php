<?php
header('Content-Type: text/html; charset=UTF-8');
$f = new Functions();

if ($this->getData('num')) {

    $num  = preg_replace('/\D/', '', $this->getData('num'));
    $nome = $this->getData('nome');
    $nasc = $this->getData('nasc');
?>

<div class="col-sm-12" style="text-align:left;">

    <div class="col-sm-12 mrg-bottom">
        <b>Paciente:</b> <?= htmlspecialchars($nome) ?> - <?= $f->BRdateFormat($nasc) ?>
    </div>

    <div class="col-sm-12">
        <label>Especialidade</label>
        <?= $f->select(
            Daoagendas::slctEspecs(),
            "select mrg-bottom",
            "slct-espec-whatsapp",
            "slct-espec-whatsapp",
            "",
            "id",
            "especialidade",
            "",
            null,
            "",
            "",
            "",
            true,
            "Selecione a Especialidade"
        ) ?>
    </div>
    <div class="col-sm-12">
        <label>Data da consulta</label>
                <input type="text" class="form-control calendar date mrg-bottom" id="inp-data-whatsapp" name="inp-data-whatsapp" data-rule-required="true" data-msg-required="Selecione a Data" placeholder="Selecione uma data" value="">
    </div>
      <div class="col-sm-12">
        <label>Hora</label>
                <select class="select mrg-bottom" name="slct-hora-whatsapp" id="slct-hora-whatsapp" class="form-select">
                        <option value="">Selecione a hora</option>
                        <?php
                        $inicio = new DateTime('07:00');
                        $fim    = new DateTime('19:00');

                        while ($inicio <= $fim) {
                            $hora = $inicio->format('H:i');
                            echo "<option value=\"$hora\">$hora</option>";
                            $inicio->modify('+10 minutes');
                        }
                        ?>
                </select>
    </div>
    <br>

    <button class="btn btn-success" onclick="abrirWhatsLocal()">
        WhatsApp <i class="bi bi-whatsapp"></i>
    </button>

</div>

<script>
function abrirWhatsLocal() {

    const numero = "55<?= $num ?>";
    const nome   = <?= json_encode($nome) ?>;
    const selectData = document.getElementById("slct-espec-whatsapp");
    const espec = selectData.options[selectData.selectedIndex].text;
    const selectHora = document.getElementById("slct-hora-whatsapp");
    const hora = selectHora.options[selectHora.selectedIndex].text;
    const data = document.getElementById("inp-data-whatsapp").value;

    if (!espec) {
        alert("Selecione a especialidade.");
        return;
    }

    const msg =
        "Olá " + nome + "! Aqui é do AME de Peruibe.\n\n" +
        "Estamos confirmando sua consulta de " + espec + ", agendada para:\n" +
        "Data: " + data + "\n" +
        "Hora: " + hora + "\n" +
        "Responda:\n\n" +
        "1 – Confirmo presença\n" +
        "2 – Não poderei comparecer\n\n" +
        "Sua resposta é muito importante para evitar faltas.\n\n" +
        "Obrigado.";

    const link = "https://wa.me/" + numero + "?text=" + encodeURIComponent(msg);
    window.open(link, "_blank");
}
</script>

<?php
} else {
    echo "Selecione um contato!";
}
?>