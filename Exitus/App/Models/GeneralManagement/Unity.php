<?php

namespace App\Models\GeneralManagement;

use MF\Model\Model;

class Unity extends Model
{

    private $unityId;


    public function __get($att)
    {
        return $this->$att;
    }


    public function __set($att, $newValue)
    {
        return $this->$att = $newValue;
    }

    /**
     * Retorna as unidades disponíveis para uso, com base na configurações definidas
     * 
     * @return void
     */
public function readOpenUnits()
{
    // Passo 1: obtém o controle mais recente
    $query = "SELECT fk_id_controle_unidade FROM configuracao ORDER BY id_configuracao DESC LIMIT 1";
    $stmt = $this->db->prepare($query);
    $stmt->execute();
    $controle = $stmt->fetchColumn();

    // Passo 2: monta a consulta com base no valor retornado
    if ($controle == 1) {
        $sql = "SELECT id_unidade AS option_value, unidade AS option_text FROM unidade WHERE id_unidade = 1";
    } elseif ($controle == 2) {
        $sql = "SELECT id_unidade AS option_value, unidade AS option_text FROM unidade WHERE id_unidade BETWEEN 1 AND 2";
    } else {
        $sql = "SELECT id_unidade AS option_value, unidade AS option_text FROM unidade WHERE id_unidade <> 0";
    }

    // Passo 3: executa a consulta final
    return $this->speedingUp($sql);
}




    /**
     * Retorna uma única unidade pelo id
     * 
     * @return void
     */
    public function searchSpecificUniy()
    {

        $query =

            "SELECT unidade.id_unidade AS option_value , unidade.unidade AS option_text
                
            FROM unidade 
            
            WHERE 
            
            CASE WHEN :unityId = 0 THEN unidade.id_unidade <> 0 ELSE unidade.id_unidade = :unityId END
            
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':unityId', $this->__get('unityId'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
}
