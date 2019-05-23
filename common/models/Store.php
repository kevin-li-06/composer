<?php

namespace common\models;

use Yii;
use common\models\Appointment;
use common\models\User;

/**
 * This is the model class for table "store".
 *
 * @property string $id
 * @property string $storename
 * @property string $store_code
 * @property string $address
 * @property integer $status
 * @property integer $store_code
 */
class Store extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'store';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            // [['dyson','cup','tool'], 'safe'],
            [['storename', 'store_code', 'address'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'storename' => '店名 ',
            'address' => '详细地址',
            'status' => '门店状态',
            'store_code' => '核销码',
        ];
    }

     /**
     * @author bob.qiu
     * 查询出所有店名信息
     */
    public static function getAllStore()
    {
//         $stores = self::find()->with(['p', 'c'])->where(['status' => 1])->all();
//         $result = [];
//         foreach ($stores as $store) {
//             $province = $store->p->ProvinceName;
//             $city = $store->c->CityName;
//             $result[$province][$city][] = [
//                                             'id'=>$store['id'],
//                                             'storename'=>$store['storename'],
//                                             'address'=> $store['address'],
//                                         ];            
//         }
        $stores = self::find()->where(['status' => 1])->all();
    
        return $stores;
    }

    // 关联省
    public function getP()
    {
        return $this->hasOne(Province::className(), ['ProvinceID' => 'province']);
    }

    // 关联市
    public function getC()
    {
        return $this->hasOne(City::className(), ['CityID' => 'city']);
    }

    /**
     * @author bob.qiu
     * 传入storecode 得到storeid
     * 
     */
    public static function get_sotre_id ($store_code)
    {   
        $store_id = self::find()->where(['store_code' => $store_code])->select('id')->scalar();
        return $store_id;
    }

    /**
     * @author bob.qiu
     * @param $sotre_id 门店id 
     * @param $prize_id 奖品id
     * @return 门店预约量减去门店已有库存  门店所需要补充的库存(值为负值，说明库存充盈)
     */
    public static function get_store_stock($store_id, $prize_id)
    {
        // 查询门店预约量
        $order_num = Appointment::find()->where(['store_id' => $store_id,'prize_id' => $prize_id])->count();
        //门店库存量
        $store_stock = \backend\models\StoreStock::find()->where(['store_id'=>$store_id,'prize_id'=>$prize_id])->select('stock')->scalar();
        $result = $order_num - $store_stock;
        return $result;
    }
}
