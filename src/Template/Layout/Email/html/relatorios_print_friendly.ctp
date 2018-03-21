<?php
    // dd($this->Url->build(['controller' => 'Visitas', 'action' => 'index', ''], ['fullbase' => true]));
    $urlSistemaPlain = $this->Url->build($urlSistema, ['fullBase' => true]);
?>

<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Título</title>

    <style>
        p {
            font-size: 15px;
        }
        @media only screen and (max-width: 701px) {
            p {
                font-size: 17px;
            }
        }
    </style>

  </head>
  <body style="background-color: #F6F6F6; width: 100%;">

        <table border="0" cellpadding="0" cellspacing="0" width="700" align="center">
            <tbody>
                <tr>
                    <td>
                        <table border="0" cellpadding="20" cellspacing="0" width="100%" bgcolor="#FFFFFF" style="margin-top: 35px; margin-bottom: 35px;">
                            <tbody>
                                <tr>
                                    <td>
                                        <?= $this->fetch('textoPrincipal') ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

  </body>
</html>
