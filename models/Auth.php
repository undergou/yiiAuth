<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "auth".
 *
 * @property int    $id
 * @property int    $user_id
 * @property string $source
 * @property string $source_id
 * @property User   $user
 */
class Auth extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'auth';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['user_id', 'source', 'source_id'], 'required'],
            [['user_id'], 'integer'],
            [['source', 'source_id'], 'string', 'max' => 64],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'id'        => 'ID',
            'user_id'   => 'User ID',
            'source'    => 'Source',
            'source_id' => 'Source ID',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
