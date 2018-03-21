<?php

namespace App\Model\Validation;

class KoletorProvider
{

    public function uniqueOnGrupoConditions($value, $args, $context)
    {
        dd($value);
        $conditions = [
            'nome' => $entity->nome,
            'deletado' => false,
            'grupo_id' => $entity->grupo_id
        ];

        if (!$entity->isNew()) {
            $conditions['nome !='] = $entity->getOriginal('nome');
        }

        return $conditions;
    }

    public function cpf($value, $args, $context)
    {
        $value = preg_replace('/[^0-9]/', '', (string) $value);
        // Valida tamanho
        if (strlen($value) != 11)
            return false;
        // Calcula e confere primeiro dígito verificador
        for ($i = 0, $j = 10, $soma = 0; $i < 9; $i++, $j--)
            $soma += $value{$i} * $j;
        $resto = $soma % 11;
        if ($value{9} != ($resto < 2 ? 0 : 11 - $resto))
            return false;
        // Calcula e confere segundo dígito verificador
        for ($i = 0, $j = 11, $soma = 0; $i < 10; $i++, $j--)
            $soma += $value{$i} * $j;
        $resto = $soma % 11;

        return $value{10} == ($resto < 2 ? 0 : 11 - $resto);
    }

    public function cnpj($value, $context)
    {
        $value = preg_replace('/[^0-9]/', '', (string) $value);
    	// Valida tamanho
    	if (strlen($value) != 14)
    		return false;
    	// Valida primeiro dígito verificador
    	for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++)
    	{
    		$soma += $value{$i} * $j;
    		$j = ($j == 2) ? 9 : $j - 1;
    	}
    	$resto = $soma % 11;
    	if ($value{12} != ($resto < 2 ? 0 : 11 - $resto))
    		return false;
    	// Valida segundo dígito verificador
    	for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++)
    	{
    		$soma += $value{$i} * $j;
    		$j = ($j == 2) ? 9 : $j - 1;
    	}
    	$resto = $soma % 11;

    	return $value{13} == ($resto < 2 ? 0 : 11 - $resto);
    }
}
