<?= $this->start('textoPrincipal') ?>

    <h2>Auditoria em <?= $visita->loja->nome ?>, encerrada em <?= ($visita->dt_encerramento) ? $visita->dt_encerramento->format('d/m/y \à\s H:i') : '-' ?></h2>

    <br>

    <table style="width: 100%;">
        <tbody>
            <tr>
                <td style="text-align: center; width: 33.33%" valign="top">
                    <h4>Máximo Possível</h4>
                    <h1>
                        <?= $visita['atingimento']['maximo_possivel'] ?> pts
                    </h1>
                </td>
                <td style="text-align: center; width: 33.33%" valign="top">
                    <h4>Mínimo Esperado</h4>
                    <h1>
                        <?= $visita->checklist->minimo_esperado ?>%
                    </h1>
                </td>
                <td style="text-align: center; width: 33.33%; color: <?= ($visita['atingimento']['diferenca'] >= 0) ? '#62aa3b' : '#c92e30' ?>" valign="top">
                    <h4>Atingido</h4>
                    <h1>
                        <?= $visita['atingimento']['atingido'] ?>pts (<?= $visita['atingimento']['atingido_porcentagem'] ?>%)
                    </h1>
                    <h4><?= abs($visita['atingimento']['diferenca']) ?>% <?= ($visita['atingimento']['diferenca'] >= 0) ? 'acima' : 'abaixo' ?> do mínimo esperado</h4>
                </td>
            </tr>
        </tbody>
    </table>

    <br>
    <p>O(A) auditor(a) <strong><em><?= $visita->usuario->nome ?></em></strong> acabou de finalizar uma visita e alguns itens críticos foram identificados, os quais seguem abaixo para conhecimento e providências:</p>

    <br>

    <?php if ($visita->observacao): ?>
        <br>
        <p>
            <strong>[OBSERVAÇÃO GERAL]</strong>
        </p>
        <p>
            <em>
                <?= nl2br(h($visita->observacao)) ?>
            </em>
        </p>
        <br>
    <?php endif; ?>

    <?php foreach ($perguntasComRespostasCritica as $pergunta): ?>
        <p style="text-transform: uppercase"><strong>[<?= h($pergunta->setor->nome) ?>] <?= h($pergunta->pergunta) ?></strong></p>

        <ul>
            <?php foreach ($pergunta->alternativas as $alternativa): ?>
                <?php if ($alternativa->selecionada): ?>
                    <li>
                        <?= h($alternativa->alternativa) ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>

        </ul>

        <?php if ($pergunta->resposta->fotos_requeridas): ?>

            <table width="100%" cellpadding="5">
                <tbody>
                    <?php
                        $i = 0;
                        $totalFotos = count($pergunta->resposta->fotos_requeridas);
                        $passadas = 1;
                    ?>
                    <?php foreach ($pergunta->resposta->fotos_requeridas as $fotoRequerida): ?>
                        <?php if ($i == 0): ?>
                            <tr>
                        <?php endif; ?>
                                <td width="200" align="left">
                                    <?php $urlFoto = $this->Url->build($fotoRequerida->folder . $fotoRequerida->filename, ['fullBase' => true]) ?>

                                    <a href="<?= $urlFoto ?>" target="_blank">
                                        <img src="<?= $urlFoto ?>" width="200">
                                    </a>
                                </td>
                        <?php
                            $i++;
                            if ($i == 3) {
                                $i = 0;
                            }
                        ?>
                        <?php if ($i == 0 || $passadas == $totalFotos): ?>
                            </tr>
                        <?php endif; ?>
                        <?php
                            $passadas++;
                        ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php endif; ?>

        <br style="clear: both;">

        <?php if ($pergunta->resposta->observacao): ?>
            <ul style="list-style-type: none;">
                <li>
                    <?= nl2br(h($pergunta->resposta->observacao)) ?>
                </li>
            </ul>
        <?php endif; ?>

        <br>
    <?php endforeach; ?>
<?= $this->end() ?>
