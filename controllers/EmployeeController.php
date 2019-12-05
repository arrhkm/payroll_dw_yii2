<?php

namespace app\controllers;

use Yii;
use app\models\Employee;
use app\models\EmployeeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\models\ImportForm;
use yii\web\UploadedFile;
use moonland\phpexcel\Excel;
//use yii\data\ArrayDataProvider;

/**
 * EmployeeController implements the CRUD actions for Employee model.
 */
class EmployeeController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Employee models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EmployeeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Employee model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionEmprev(){
        $model = New ImportForm();
        if (Yii::$app->request->isPost) {            
            $model->excelFile = UploadedFile::getInstance($model, 'excelFile');            
            if ($model->upload()) { // file is uploaded successfully  
                $fileName = Yii::$app->basePath.'/web/upload_file/'.$model->excelFile->name;
                $data = Excel::import($fileName, [
                    'setFirstRecordAsKeys' => true, // if you want to set the keys of record column with first record, if it not set, the header with use the alphabet column on excel. 
                    //'setIndexSheetByName' => true, // set this if your excel data with multiple worksheet, the index of array will be set with the sheet name. If this not set, the index will use numeric. 
                    'getOnlySheet' => 'Sheet1', // you can set this property if you want to get the specified sheet from the excel data with multiple worksheet.
                ]);                             
                unlink($fileName);//Hapus file nya... 

                if(isset($data)){
                    $dataArray = [];
                    $data_kosong = []; 
                    $dt_gagal_save = [];                   
                    foreach ($data as $dt){
                        //$emp_id = $dt['lama'];
                        $emp = Employee::find()->where(['emp_id'=>$dt['lama']]);
                        if ($emp->exists()){
                            $emp = $emp->one();
                            $emp->emp_id = $dt['baru'];
                            if ($emp->save()){
                                array_push($dataArray, [
                                    'emp_id_lama'=>$dt['lama'],
                                    'emp_id_baru'=>$dt['baru'],                                                               
                                ]);
                            }else{
                                array_push($dt_gagal_save, [
                                    'emp_id_lama'=>$dt['lama'],
                                    'emp_id_baru'=>$dt['baru'],                                                               
                                ]);
                            }                            

                        }else {
                            array_push($data_kosong, [
                                'emp_id_lama'=>$dt['lama'],
                                'emp_id_baru'=>$dt['baru'],
                                                          
                            ]);
                        }                        
                    }
                    return $this->render('emprev',[
                        'model'=>$model,
                        'data'=>$dataArray,
                        'data_kosong'=>$data_kosong,
                        'data_gagal_save'=>$dt_gagal_save,
                    ]);
                }
            }
        }
        return $this->render('emprev',[
            'model'=>$model,
            'data'=>$dataArray,
        ]);
        
    }

    /**
     * Creates a new Employee model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Employee();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->emp_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Employee model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->emp_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Employee model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Employee model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Employee the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Employee::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
