<?php

use yii\helpers\Html;
use yii\grid\GridView;

use app\components\Leave;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SplSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'spl';
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = ['label' => 'SPL perday', 'url' => ['splperday']];
?>
<div class="spl-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Spl', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Import Spl', ['importspl'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'employee_emp_id',
            'employee.emp_name',
            'date_spl',
            'start_lembur',
            'end_lembur',
            [
                'label'=>'Duration',
                'value'=>function($model){
                    $leave = New Leave($model->start_lembur, $model->end_lembur);
                    return $leave->getDurationJam();
                }
            ],
            //'so',
            //'nama_pekerjaan',
            //'qty',
            //'satuan',
           

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
