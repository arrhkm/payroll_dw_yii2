<?php

namespace app\controllers;

use app\components\hkm\HkmLib;
use app\models\Absensi;
use app\models\DownloadMachineForm;
use app\models\HsHrEmpAbsensi;
use Yii;
use app\models\MachineAtt;
use app\models\MachineAttSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

/**
 * MachineattController implements the CRUD actions for MachineAtt model.
 */
class MachineattController extends Controller
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
     * Lists all MachineAtt models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MachineAttSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MachineAtt model.
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
     * Creates a new MachineAtt model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MachineAtt();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing MachineAtt model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing MachineAtt model.
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
     * Finds the MachineAtt model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MachineAtt the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MachineAtt::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionDownload($id)
    {
        $machine = $this->findModel($id);  
        //$machine = Machineattendance::findOne($id);
        
        //$ip = '192.168.4.138';
        $ip = $machine->ip;

        //$port='80';
        $port= $machine->port;		
        //$com=0;
        $com = $machine->com;

        try {
                //$log_data = Yii::$app->hkmlib->download($ip, $port, $com);
                $x = New HkmLib();
                $log_data = $x->download($ip, $port, $com);


                
        } catch (Exception $e){
                echo $e->getMessage();
        } 
        
        //var_dump($log_data);
        //----------------
        /*$lastlog = Attlog::find()->where(['id_attmachine'=>$id])
                ->andWhere(['<>', 'status', 100])
                ->andWhere(['<>', 'verified', 100])
                ->max('date_log');
        */
        
        $lastlog = HsHrEmpAbsensi::find()->where(['id_machine'=>$id])->max('timestamp');
        
        $array_log=array();
        $first_id = HsHrEmpAbsensi::getLastId();
        //Populate $log_data To Array $array_log with id_log Auto Increment
        foreach ($log_data['Row'] as $value){  
            if (strtotime($value['DateTime']) > strtotime($lastlog)){
                array_push($array_log,[
                    //'id'=>$first_id,
                    'id'=>$value['PIN'],
                    'timestamp'=>$value['DateTime'],
                    'verifikasi'=>$value['Verified'],
                    'status'=>$value['Status'],                   
                    'id_machine'=>$id,
                ]);
                $first_id++;
                
            }   
        }
        //var_dump($array_log);
        
        //-- INSERT Data INTO table Attlog with bacht insert PHP Yii2
        Yii::$app->db->createCommand()->batchInsert(
        'hs_hr_emp_absensi', 
        [
            'id',
            'timestamp', 
            'verifikasi',
            'status',            
            'id_machine'
        ],$array_log)->execute();
        //--END INSERT
        //
        //var_dump($log_data);
        $dataProvider = new ArrayDataProvider([
            'allModels' => $array_log,//$log_data['Row'],
            'pagination' => [
                'pageSize' => 500,
            ],	

        ]);
        
        return $this->render('download',[        
            'rows'=>$dataProvider,
            'machine' => $machine, 
            'x'=>$log_data,
            'array_log'=>$array_log,
            'lastlog'=>$lastlog,
        ]);
       
         	
    }

    public function actionIntegrasi(){
        $model = New DownloadMachineForm();

        if ($model->load(Yii::$app->request->post())){
            $tgl = $model->start_date." s/d ".$model->end_date;
            $absensi = HsHrEmpAbsensi::find()->where(['Between', 'date(timestamp)', $model->start_date, $model->end_date])->all();
            $mylog = ArrayHelper::toArray($absensi);

            $my_log = [];
            foreach ($absensi as $log){
                array_push($my_log, ['id'=>$log->id, 'timestamp'=>$log->timestamp, 'verifikasi'=>$log->verifikasi, 'status'=>$log->status]);
            }
            

            return $this->render('integration',[
                'model'=>$model,
                'tgl'=>$tgl,
                'absensi'=>$mylog,
            ]);
        }

        return $this->render('integration',[
            'model'=>$model,
        ]);
    }
}
