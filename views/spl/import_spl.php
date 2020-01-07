<?php 

use yii\widgets\ActiveForm;
use yii\grid\GridView;
?>


<?php 
$this->params['breadcrumbs'][] = ['label' => 'spl', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="">

<h4> Contoh Table CSV</h4>
<table class="table">
    <thead>
      <tr>
        <th>emp_id</th>
        <th>start_lembur</th>
        <th>end_lembur</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>P0027</td>
        <td>2019-12-10 15:00:00</td>
        <td>2019-12-10 20:00:00</td>
      </tr>
      <tr>
        <td>P0027</td>
        <td>2019-12-10 15:00:00</td>
        <td>2019-12-10 20:00:00</td>
      </tr>
    </tbody>
  </table>
</div>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

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
            'date_spl',
            'start_lembur',
            'end_lembur',           
        ],
    ]);
    
    //var_dump($data);
    
   
}
