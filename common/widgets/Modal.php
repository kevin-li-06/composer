<?php
namespace common\widgets;
use yii\helpers\Html;
use Yii;

class Modal extends \yii\bootstrap\Widget
{
    public $modal;
    public function init()
    {
        parent::init();
        if ($this->modal === null){
//            $this->modal = '
//                 <button type="button" class="btn btn-primary" data-toggle="modal" data-target=".bs-example-modal-lg">Large modal</button>
//                <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
//                  <div class="modal-dialog modal-lg" role="document">
//                    <div class="modal-content">
//                      ...
//                    </div>
//                  </div>
//                </div>';
        }
    }

    public function run()
    {
        return $this->render('Modal');
    }
}