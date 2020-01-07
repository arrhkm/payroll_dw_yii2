<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "plusmin_gaji".
 *
 * @property int $kd_plusmin
 * @property int $kd_periode
 * @property string $emp_id
 * @property string $tgl_plusmin
 * @property string $jml_plus
 * @property string $jml_min
 * @property string $ket
 */
class PlusminGaji extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'plusmin_gaji';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kd_plusmin', 'kd_periode', 'emp_id', 'tgl_plusmin', 'ket'], 'required'],
            [['kd_plusmin', 'kd_periode'], 'integer'],
            [['tgl_plusmin'], 'safe'],
            [['jml_plus', 'jml_min'], 'number'],
            [['emp_id'], 'string', 'max' => 15],
            [['ket'], 'string', 'max' => 50],
            [['kd_plusmin'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kd_plusmin' => 'Kd Plusmin',
            'kd_periode' => 'Kd Periode',
            'emp_id' => 'Emp ID',
            'tgl_plusmin' => 'Tgl Plusmin',
            'jml_plus' => 'Jml Plus',
            'jml_min' => 'Jml Min',
            'ket' => 'Ket',
        ];
    }
}
