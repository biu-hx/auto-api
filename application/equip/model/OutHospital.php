<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/1 0001
 * Time: 14:13
 */

namespace app\equip\model;


use think\Db;

class OutHospital
{
    /**
     * @param $detail
     */
    public function addOutHospital($detail)
    {
        $ddd = $detail['baseInfo'];
        $insert = [
            'user_name' => isset($ddd['UserName']) ? $ddd['UserName'] : '',
            'reg_no' => $ddd['RegNo'],
            'curent_dept' => $ddd['CurentDept'],
            'adm_date' => $ddd['AdmDate'],
            'bed_no' => $ddd['BedNo'],
            'deposit_total' => $ddd['DepositTotal'],
            'total_amount' => $ddd['TotalAmount'],
            'balance' => $ddd['Balance'],
            'state' => $ddd['State'],
            'bank_no' => $ddd['BankNo'],
            'bank_name' => $ddd['BankName'],
            'adm_id' => $ddd['AdmID'],
            'need_pay' => $ddd['needPay'],
            'detail' => $detail['all_list'] ? json_encode($detail['all_list']) : json_encode([]),
        ];
        Db::name('out_hospital')->insert($insert);
        unset($detail,$ddd,$insert);
    }
}