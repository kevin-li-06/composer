<?php

namespace common\models;

use backend\components\Loyalty;
use Yii;
use yii\base\Model;
use common\components\ArrayDataProvider;
use common\models\User;

/**
 * UserSearch1 represents the model behind the search form about `common\models\User`.
 */
class UserSearch1 extends Model
{

    public $code;
    public $mobile;
//    public

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code'],'required'],
            [['id', 'small_chance', 'big_chance', 'continuous', 'redeem_prize', 'redeem_at', 'created_at', 'updated_at', 'change_prize', 'change_at', 'result'], 'integer'],
            [['username', 'region', 'auth_key', 'mobile', 'mobile_hash', 'card', 'openid'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function attributeLabels()
    {
        return [
          'code' => 'code',
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $pageSize = Yii::$app->request->get('per-page', 10);
        $pageNo = Yii::$app->request->get('page', 1);
        //组装多数据模糊分页查询
        if ($this->load($params)){
            foreach ($params['CustomerSearch'] as $key => $value){
                if (!empty($value)){
                    $data[$key] = $value;
                }
            }
            $data['search_model'] = 'contain';
            $data[ 'from_date'] = '2015-01-01 00:00:00';
            $customers = Loyalty::customer_list($data);
            if ($customers['status'] != 'error'){
                $count = $customers['total'];
                $customers = isset($customers['customers']) ? $customers['customers'] : [];
                foreach ($customers as $k => $v){
                    $userInfo[] = $customers[$k];
                }
                $itmes = $userInfo;
            } else {
                $itmes = [];
            }
        } else {
            //没有模糊查询直接分页
            $customers = Loyalty::customer_list(['offset' => ($pageNo-1)*$pageSize, 'limit' => $pageSize,'from_date' => '2015-01-01 00:00:00']);
//            echo "<pre/>";
//            var_dump($customers);die;
            $count = $customers['total'];
            $customers = $customers['customers'];
            foreach ($customers as $k => $v){
                $userInfo[] = $customers[$k];
            }
            $itmes = $userInfo;
        }


        $dataProvider = new ArrayDataProvider([
//            'key' => 'code',
            'allModels' => $itmes,
            'totalCount' => $count,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);
        return $dataProvider;
    }
}
