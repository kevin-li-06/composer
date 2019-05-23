<?php

namespace backend\controllers;

use Yii;
use backend\components\Wechat;
use backend\components\Log;
use backend\components\BaseAdminController;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;


/**
 * UserController implements the CRUD actions for User model.
 */

class WechatController extends BaseAdminController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionMenuGet()
    {
        $re = Wechat::menu_get();

        return $this->render('menu_get', [
            're' => $re,
        ]);
    }

    // 设置微信菜单
    public function actionMenuSet()
    {
        $appid = Wechat::config('appid');
        $component_appid = Wechat::config('component_appid');
			
        // 第三方OAuth授权链接设置
        // 参考 config/web.php 中的 urlManager 的配置
        // 'wechat/oauth/<method:\w+>' => 'wechat/oauth',
        // method 参数的格式 controller_action
        // $state = 'http://winshare.onthemooner.com/index.php/wechat/oauth/member_index';
        $state = 'https://wechat.xgate.com/index.php/wechat/oauth/member_index';

        $center_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . 
        '&redirect_uri=https%3A%2F%2Fwcapi.xgatecorp.com%2Fwechat%2Fauth%2Furl&response_type=code&scope=snsapi_base&state=' . $state . '&component_appid=' . $component_appid . '#wechat_redirect';

        $lottery_state = 'http://jebsen.onthemooner.com';
        $lottery_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . 
        '&redirect_uri=https%3A%2F%2Fwcapi.xgatecorp.com%2Fwechat%2Fauth%2Furl&response_type=code&scope=snsapi_base&state=' . $lottery_state . '&component_appid=' . $component_appid . '#wechat_redirect';

        $lottery_state_p = 'https://campaigns.xgatecorp.com';
        $lottery_url_p = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . 
        '&redirect_uri=https%3A%2F%2Fwcapi.xgatecorp.com%2Fwechat%2Fauth%2Furl&response_type=code&scope=snsapi_base&state=' . $lottery_state_p . '&component_appid=' . $component_appid . '#wechat_redirect';
        // $lottery_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . 
        // '&redirect_uri=' . urlencode('http://jebsen.onthemooner.com/index.php/member/index') . '&response_type=code&scope=snsapi_base&state=wechat#wechat_redirect';

		$data = 
		[
			'button' => 
			[
				[
					'type' => 'view',
					'name' => '关于XGATE',
					'url' => 'http://www.xgate.com',
					'sub_button' => [],
				],
				[
					'name' => '抽奖活动',
					'sub_button' => 
					[
						[
							'type' => 'view',
							'name' => 'Onthemooner',
							'url' => $lottery_url,
							'sub_button' => [],
                        ],
                        [
							'type' => 'view',
							'name' => 'Jebsen',
							'url' => $lottery_url_p,
							'sub_button' => [],
                        ],
                        // [
						// 	'type' => 'click',
						// 	'name' => '点击事件',
						// 	'key' => 'JP',
						// 	'sub_button' => [],
						// ],
					]
                ],
                // [
                //     'name' => '轩客会',
                //     'sub_button' => 
                //     [
                //         [
                //             'type' => 'view',
                //             'name' => '会员中心',
                //             'url' => $center_url,
                //             'sub_button' => [],
                //         ],
                //     ]
                // ],
			],
        ];
        $re = '';
        
        if (Yii::$app->request->isPost) {
            $re = Wechat::menu_set($data);
        }
 
        return $this->render('menu_set', [
            'data' => $data,
            're' => $re,
        ]);
    }
}
