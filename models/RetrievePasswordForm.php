<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\Url;

/**
 * @property User|null $user This property is read-only.
 */
class RetrievePasswordForm extends Model
{
    /**
     * @var string
     */
    public $email;

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
            [['email'], 'required'],
        ];
    }

    /**
     * @return void
     */
    public function recovery(): void
    {
        if ($this->validate() && $this->getUser() !== null) {
            $link = sprintf('%s/change-password?rec=%s', Url::base('http'), $this->getUser()->getResetKey());
            $text = sprintf('Recovery password: <a href="%s">%s</a>', $link, $link);
            Yii::$app->mailer->compose()
                ->setFrom('user-auth@mail.ru')
                ->setTo($this->email)
                ->setSubject('Recovery password')
                ->setTextBody($text)
                ->send()
            ;
        }
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
