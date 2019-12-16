<?php

namespace app\models;

use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "auth_item".
 *
 * @property string   $name
 * @property int      $type
 * @property string   $description
 * @property string   $rule_name
 * @property resource $data
 * @property int      $created_at
 * @property int      $updated_at
 */
class AuthItem extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'auth_item';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['name', 'type'], 'required'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64],
            [['name'], 'unique'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'name'        => 'Name',
            'type'        => 'Type',
            'description' => 'Description',
            'rule_name'   => 'Rule Name',
            'data'        => 'Data',
            'created_at'  => 'Created At',
            'updated_at'  => 'Updated At',
        ];
    }

    /**
     * @return ActiveQuery
     *
     * @throws InvalidConfigException
     */
    public function getUsers(): ActiveQuery
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])
            ->viaTable('auth_assignment', ['item_name' => 'name'])
            ;
    }
}
