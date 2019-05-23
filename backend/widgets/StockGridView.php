<?php

namespace backend\widgets;

use Yii;
use yii\grid\GridView;
use yii\helpers\Html;

class StockGridView extends GridView
{
    public $filterPosition = self::FILTER_POS_BODY;

    // 复写头部
    public function renderTableHeader()
    {
        $content = '<tr>
                        <th></th>
                        <th colspan="3">戴森吹风机</th>
                        <th colspan="3">双立人刀具</th>
                        <th colspan="3">康宁保温杯</th>
                        <th colspan="3">星巴克咖啡</th>
                        <th colspan="3">抹布</th>
                        <th colspan="3">冰箱贴</th>
                    </tr>';

        $cells = [];
        foreach ($this->columns as $column) {
            /* @var $column Column */
            $cells[] = $column->renderHeaderCell();
        }
        $content .= Html::tag('tr', implode('', $cells), $this->headerRowOptions);
        if ($this->filterPosition === self::FILTER_POS_HEADER) {
            $content .= $this->renderFilters() . $content;
        } elseif ($this->filterPosition === self::FILTER_POS_BODY) {
            $content .= $this->renderFilters();
        }
        // echo '<pre>';print_r($content);exit;

        return "<thead>\n" . $content . "\n</thead>";
    }

    // 复写过滤
    public function renderFilters()
    {
        if ($this->filterModel !== null) {
            $cells = [];
            foreach ($this->columns as $column) {
                /* @var $column Column */
                $cells[] = $column->renderFilterCell();
            }
            
            // 删除不需要的
            $cells = ['<td colspan="19"><input type="text" class="form-control" name="StockArraySearch[zh_storename]"></td>'];

            return Html::tag('tr', implode('', $cells), $this->filterRowOptions);
        }

        return '';
    }
}
