<?php

namespace app\controllers;

use Yii;
use app\models\Spl;
use app\models\SplSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\models\ImportForm;
use yii\web\UploadedFile;
use moonland\phpexcel\Excel;
use yii\data\ArrayDataProvider;

/**
 * SplController implements the CRUD actions for Spl model.
 */
class SplController extends Controller
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
     * Lists all Spl models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SplSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Spl model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Spl model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionImportspl(){
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
                   
                    foreach ($data as $dt){
                        $start = date('Y-m-d',strtotime($dt['start_lembur']));
                        $spl = Spl::find()->where(['employee_emp_id'=>$dt['emp_id'], 'date_spl'=>$start]);
                        if (!$spl->exists()){
                            $model_spl = New Spl();
                            $model_spl->scenario = Spl::SCENARIOCSV;
                            
                            $model_spl->id = $model_spl->getLastId();
                            $model_spl->employee_emp_id = $dt['emp_id'];
                            $model_spl->date_spl= $start;
                            $model_spl->start_lembur = $dt['start_lembur'];
                            $model_spl->end_lembur = $dt['end_lembur'];                                
                            
                            //if ($model_spl->validate()){
                                array_push($dataArray, [
                                    'id'=> $model_spl->id,
                                    'emp_id'=>$dt['emp_id'],
                                    'date_spl'=>$start,
                                    'start_lembur'=>$dt['start_lembur'],
                                    'end_lembur'=>$dt['end_lembur'],                                
                                ]);
                                $model_spl->save();
                                //$lembur_id ++;
                            
                            //}
                           

                            
                            
                        }
                           
                    }
                    $provider = new ArrayDataProvider([     
                        'allModels'=>$dataArray,
                        'pagination' => [
                            'pageSize' => 10000,
                        ],
                    ]);
                    return $this->render('import_spl', [
                        'model'=>$model,
                        'data'=>$dataArray,
                        'provider'=>$provider,
                    ]);
                }

            }
        }

        return $this->render('import_spl', [
            'model'=>$model,
            //'data'=>'Test',
        ]);
    }

    public function actionCreate()
    {
        $model = new Spl(['scenario'=>Spl::SCENARIOINPUT]);
        //$model->scenario = Spl::SCENARIOINPUT;
        $model->id = $model->getLastId();



        if ($model->load(Yii::$app->request->post())) {
            if (isset($model->start_lembur)){
                $date_start = date_create($model->start_lembur);
                $model->date_spl = date_format($date_start, "Y-m-d");
            }
            if ($model->validate()){
                if ($model->save()){

                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            
            
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Spl model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('scenarioinput');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Spl model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Spl model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Spl the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Spl::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
