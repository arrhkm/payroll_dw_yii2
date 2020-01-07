<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "periode".
 *
 * @property int $kd_periode
 * @property string $nama_periode
 * @property string $tgl_awal
 * @property string $tgl_akhir
 * @property int $potongan_jamsos
 */
class Periode extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'periode';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kd_periode', 'nama_periode', 'tgl_awal', 'tgl_akhir'], 'required'],
            [['kd_periode', 'potongan_jamsos'], 'integer'],
            [['tgl_awal', 'tgl_akhir'], 'safe'],
            [['nama_periode'], 'string', 'max' => 32],
            [['kd_periode'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kd_periode' => 'Kd Periode',
            'nama_periode' => 'Nama Periode',
            'tgl_awal' => 'Tgl Awal',
            'tgl_akhir' => 'Tgl Akhir',
            'potongan_jamsos' => 'Potongan Jamsos',
        ];
    }
}
