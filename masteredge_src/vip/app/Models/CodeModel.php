<?php

namespace App\Models;

use CodeIgniter\Model;

class CodeModel extends Model
{
    protected $table      = 'referral_code';
    protected $primaryKey = 'id_reff';
    protected $allowedFields = ['code', 'Ucode', 'set_saldo', 'set_level', 'used_limit', 'max_limit', 'used_by', 'created_by'];
    protected $useTimestamps = true;

    public function getCode($limit = 1, $order_by = 'DESC')
    {
        $this->limit($limit);
        $this->orderBy($this->primaryKey, $order_by);
        return $this->get()->getResultObject();
    }


    public function useReferral($code, $username = true)
      {
    $code = $this->checkCode($code);
    if ($code && $username) {
        $this->update($code->id_reff, [
            'used_by' => $username,
            'used_limit' => $code->used_limit + 1
        ]);
        return true;
    }
    return false;
   }

    
 public function checkCode($code, $dehash = true)
{
    $code = $dehash? create_password($code, false) : $code;
    $result = $this->getWhere(['code' => $code])
            ->getRowObject();
    if (!$result) {
        $result = $this->getWhere(['code' => $code, 'used_by' => null, 'used_limit <' => 'ax_limit'])->getRowObject();
    }
    return $result;
  }
    
}
