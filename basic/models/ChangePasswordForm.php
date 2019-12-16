<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\Url;

/**
 * @property User|null $user This property is read-only.
 */
class ChangePasswordForm extends Model
{
    /**
     * @var string
     */
    public $newpassword;

    /**
     * @var User|bool
     */
    private $_user = false;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['newpassword'], 'required'],
        ];
    }

    /**
     * @param User $user
     *
     * @return bool
     *
     * @throws Exception
     */
    public function change(User $user): bool
    {
        if ($this->validate()) {
            $user->setPassword($this->newpassword);
            $user->generateResetKey();
            $user->save();

            return true;
        }

        return false;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        if ($this->_user === false) {
            $this->_user = User::findByEmail($this->email);
        }

        return $this->_user;
    }

    /**
     * @return string
     */
    public function formName(): string
    {
        return 'restore';
    }
}
