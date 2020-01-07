<?php 

use yii\widgets\ActiveForm;
use yii\grid\GridView;

use kartik\select2\Select2;
?>

<?php 
$this->params['breadcrumbs'][] = ['label' => 'Plus Min', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
echo " Fromat : (emp_id, tgl_min, jml_plus, jml_min, ket)";
?>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= $form->field($model, 'id_period')->textInput(['maxlength' => true])->widget(Select2::className(),[
            'data'=>$period_list,
    ])?>

    <?= $form->field($model, 'excelFile')->fileInput() ?>

    <button>Submit</button>

<?php ActiveForm::end() ?>
<?php
if (isset($data)){
    echo GridView::widget([
        'dataProvider'=>$provider,
        'columns'=>[
            'id',
            'emp_id',
            'value',
            //'date_insentif',           
        ],
    ]);
    
    //var_dump($data);
    
   
}
