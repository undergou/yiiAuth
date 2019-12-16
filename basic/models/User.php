<?php

namespace app\models;

use Throwable;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\db\Query;
use yii\db\StaleObjectException;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property int        $id
 * @property string     $username
 * @property string     $email
 * @property string     $displayname
 * @property string     $password
 * @property string     $authKey
 * @property string     $resetKey
 * @property AuthItem[] $permissions
 */
class User extends ActiveRecord implements IdentityInterface
{
    const SCENARIO_LOGIN    = 'login';
    const SCENARIO_REGISTER = 'register';
    const SCENARIO_CREATE   = 'create';

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['username', 'password', 'permissions'], 'required', 'on' => self::SCENARIO_LOGIN],
            [
                ['username', 'displayname', 'email', 'password'],
                'required',
                'on' => self::SCENARIO_REGISTER,
            ],
            [
                ['username', 'displayname', 'email', 'password', 'authKey', 'resetKey'],
                'required',
                'on' => self::SCENARIO_CREATE,
            ],
        ];
    }

    /**
     * @return array
     */
    public function fields(): array
    {
        return [
            'displayname' => 'displayname',
        ];
    }

    /**
     * @param int|string $id
     *
     * @return User|null
     */
    public static function findIdentity($id): ?User
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @param string $email
     *
     * @return User|null
     */
    public static function findByEmail(string $email): ?User
    {
        return static::findOne(['email' => $email]);
    }

    /**
     * @param string $resetKey
     *
     * @return User|null
     */
    public static function findByResetKey(string $resetKey): ?User
    {
        return static::findOne(['resetKey' => $resetKey]);
    }

    /**
     * @param mixed $token
     * @param null  $type
     *
     * @return void
     *
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null): void
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * @param string $username
     *
     * @return User|null
     */
    public static function findByUsername(string $username): ?User
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->getPrimaryKey();
    }

    /**
     * @return string
     */
    public function getAuthKey(): string
    {
        return $this->authKey;
    }

    /**
     * @param string $authKey
     *
     * @return bool
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     *
     * @return bool if password provided is valid for current user
     */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     *
     * @return void
     *
     * @throws Exception
     */
    public function setPassword(string $password): void
    {
        $this->password = Yii::$app->getSecurity()->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     *
     * @return void
     *
     * @throws Exception
     */
    public function generateAuthKey(): void
    {
        $this->authKey = Yii::$app->security->generateRandomString();
    }

    /**
     * @return string
     */
    public function getResetKey(): string
    {
        return $this->resetKey;
    }

    /**
     * @param string $resetKey
     *
     * @return bool
     */
    public function validateResetKey(string $resetKey): bool
    {
        return $this->getResetKey() === $resetKey;
    }

    /**
     * @return void
     * @throws Exception
     *
     */
    public function generateResetKey(): void
    {
        $this->resetKey = Yii::$app->security->generateRandomString();
    }

    /**
     * @return ActiveQuery
     *
     * @throws InvalidConfigException
     */
    public function getPermissions(): ActiveQuery
    {
        return $this->hasMany(AuthItem::class, ['name' => 'item_name'])
            ->viaTable('auth_assignment', ['user_id' => 'id'])
            ;
    }

    /**
     * @param string $permName
     *
     * @return void
     *
     * @throws \yii\db\Exception
     */
    public function removePermission(string $permName): void
    {
        (new Query())->createCommand()
            ->delete('auth_assignment', ['user_id' => $this->getId(), 'item_name' => $permName])->execute()
        ;
    }

    /**
     * @return void
     *
     * @throws \yii\db\Exception
     */
    public function removeAllPermissions(): void
    {
        (new Query())->createCommand()
            ->delete('auth_assignment', ['user_id' => $this->getId()])->execute()
        ;
    }

    /**
     * @param string $permName
     *
     * @return void
     *
     * @throws \yii\db\Exception
     */
    public function addPermission(string $permName): void
    {
        (new Query())->createCommand()
            ->insert('auth_assignment', ['user_id' => $this->getId(), 'item_name' => $permName, 'created_at' => time()])
            ->execute()
        ;
    }

    /**
     * @return false|int
     *
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function delete()
    {
        $auths = Auth::findAll(['user_id' => $this->getId()]);
        foreach ($auths as $auth) {
            $auth->delete();
        }

        return parent::delete();
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'users';
    }
}
