<?php 
namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;
use app\models\ImportForm;

class FormPlusmin extends ImportForm
{
    public $id_period;

    public function rules()
    {
        return [
            [['excelFile'], 'file', 'skipOnEmpty' => false, /*'extensions' => 'xls,xlsx,csv'*/],
            //['id_period', 'require'],
            [['id_period'], 'integer'],
        ];
    }
   

}
