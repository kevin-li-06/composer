<?php
$this->registerJsFile('@web/js/echarts.min.js', ['position' => 1]);
$this->title = '统计报表';
?>

<h1><?= $this->title ?></h1>
<hr>

<!-- 为ECharts准备一个具备大小（宽高）的Dom -->

<div class="row">
    <div class="col-xs-6">
        <div id="main" style="width: 100%;height:300px;"></div>
    </div>
    <div class="col-xs-6">
        <div id="chance" style="width: 100%;height:300px;"></div>
    </div>
</div>

<hr>

<div class="row">
    <div class="col-xs-12">
        <div id="source" style="width: 100%;height:300px;"></div>
    </div>
    <div class="col-xs-12">
        <div id="continue" style="width: 100%;height:300px;"></div>
    </div>
</div>

<hr>

<table class="table table-striped">
    <h3>参与活动人数及比例</h3>
    <thead>
    <tr>
        <th>总人数</th>
        <th>参加活动总人数</th>
        <th>参加活动总人数占比</th>
        <th>参加活动总刮奖次数</th>
        <th>未中奖人数</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><?= $data['userSum']?></td>
        <td><?= $data['attendSum']?></td>
        <td><?= $data['accounted']?></td>
        <td><?= $data['personSum']?></td>
        <td><?= $data['emptySum']?></td>
    </tr>
    </tbody>
</table>
<hr/>
<table class="table table-striped">
    <h3>中奖次数及比例</h3> &nbsp;</span>
    <thead>
    <tr>
        <th>奖品等级</th>
        <th>参与刮奖次数</th>
        <th>中奖人次</th>
        <th>预约总量</th>
        <th>预约/中奖占比</th>
        <th>核销总量</th>
        <th>总库存</th>
        <th>奖品剩余量</th>
        <th>奖品剩余占比</th>
    </tr>
    </thead>
    <?php foreach ($resultLevels as $k => $v) { ?>
    <tbody>
    <tr>
        <td><?= $k ?></td>
        <td><?= $v['sum'] ?></td>
        <td><?= $v['gainSum'] ?></td>
        <td><?= $v['appointmentSum'] ?></td>
        <td><?php
            if ($k == '未中奖') {
                echo '';
            } elseif ($v['gainSum'] == 0) {
                echo '0%';
            } else {
                echo round($v['appointmentSum']/$v['gainSum'], 4) * 100 . '%';
            } ?>
        </td>
        <td><?= $v['redeemSum'] ?></td>
        <td><?= $v['stockSum'] ?></td>
        <td><?= $v['leftSum'] ?></td>
        <td><?= $v['leftPercent'] ?></td>
    </tr>
    </tbody>
    <?php } ?>
</table>
<script>
    //     基于准备好的dom，初始化echarts实例
    var myChart = echarts.init(document.getElementById('main'));
    // 指定图表的配置项和数据

    option = {
        title: {
            text: '门店预约量前10排名',
//            subtext: '数据来自网络'
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            }
        },
        legend: {
//            data: ['2011年', '2012年']
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'value',
            boundaryGap: [0, 0.01]
        },
        yAxis: {
            type: 'category',
            data: [<?php foreach ($stores as $store){ echo '"'.$store['zh_storename'].'"'.',';}?>]
        },
        series: [
            {
                name: '预约次数',
                type: 'bar',
                data: [<?php foreach ($stores as $store){ echo isset($store['sum']) ? $store['sum'].',' : 0 . ',';}?>]
            },
        ]
    };

    //     使用刚指定的配置项和数据显示图表。
    myChart.setOption(option);

    var chanceChart = echarts.init(document.getElementById('chance'));

    chance_option = {
        title: {
            text: '门店增加大奖机会次数前10排名',
//            subtext: '数据来自网络'
        },
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            }
        },
        legend: {
//            data: ['2011年', '2012年']
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            // name: '大奖机会的次数',
            type: 'value',
            boundaryGap: [0, 1]
        },
        yAxis: {
            name: '门店名字',
            type: 'category',
            data: [
                <?php foreach ($store_bigchance as $key => $chancedata){ echo '"'.$chancedata['zh_storename'].'"'.',';}?>
                ]
        },
        series: [
            {
                name: '大奖机会的次数',
                type: 'bar',
                itemStyle: {
                    normal: {
                        color: '#ffbe55'
                    }
                },
                data: [<?php foreach ($store_bigchance as $chancedata){ echo isset($chancedata['bignum']) ? $chancedata['bignum'].',' : 0 . ',';}?>]
            },
        ]
    };

    chanceChart.setOption(chance_option);

    var sourceChart = echarts.init(document.getElementById('source'));

    source_option = {
        title: {
            text: '参与刮奖的来源分布',
        },
        tooltip : {
            trigger: 'axis',
            axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
            }
        },
        legend: {
            data:['签到','签到7天','分享','消费']
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis : [
            {
                type : 'category',
                data : [<?php foreach ($sourceSum['date'] as $date){ echo '"' . $date . '",';}?>]
            }
        ],
        yAxis : [
            {
                type : 'value'
            }
        ],
        series : [
            {
                name:'签到',
                itemStyle: {
                    normal: {
                        color: '#5cb85c'
                    }
                },
                type:'bar',
                stack: '来源',
                data:[<?php foreach ($sourceSum['checkin'] as $date){ echo $date . ',';}?>]
            },
            {
                name:'签到7天',
                itemStyle: {
                    normal: {
                        color: '#f0ad4e'
                    }
                },
                type:'bar',
                stack: '来源',
                data:[<?php foreach ($sourceSum['seven'] as $date){ echo $date . ',';}?>]
            },
            {
                name:'分享',
                itemStyle: {
                    normal: {
                        color: '#5bc0de'
                    }
                },
                type:'bar',
                stack: '来源',
                data:[<?php foreach ($sourceSum['share'] as $date){ echo $date . ',';}?>]
            },
            {
                name:'消费',
                itemStyle: {
                    normal: {
                        color: '#d9534f'
                    }
                },
                type:'bar',
                stack: '来源',
                data:[<?php foreach ($sourceSum['consumption'] as $date){ echo $date . ',';}?>]
            }
        ]
    };

    sourceChart.setOption(source_option);

    var continueChart = echarts.init(document.getElementById('continue'));

    continue_option = {
        title: {
            text: '连续签到的天数及人次',
        },
        tooltip : {
            trigger: 'axis',
            axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
            }
        },
        legend: {
            data:['连续签到']
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis : [
            {
                type : 'category',
                data : [<?php foreach ($continue as $v){ echo '"连续签到' . $v['continuous'] . '天",';}?>]
            }
        ],
        yAxis : [
            {
                type : 'value'
            }
        ],
        series : [
            {
                name:'人次',
                type:'bar',
                stack: '来源',
                itemStyle: {
                    normal: {
                        color: '#d36d69'
                    }
                },
                data:[<?php foreach ($continue as $v){ echo $v['sum'] . ',';}?>]
            },
        ]
    };

    continueChart.setOption(continue_option);
</script>

