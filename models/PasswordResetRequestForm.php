<?php
namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;

class PasswordResetRequestForm extends Model
{
    public $email;

    public function rules()
    {
        return [['email', 'required', 'message' => 'Заполните это поле'], ['email', 'email', 'message' => 'Введите корректный email']];
    }

    public function sendEmail()
    {
        $user = User::findOne(['email' => $this->email]);
        if (!$user) {
            return false;
        }

        // Генерация токена (можно хранить в отдельном поле, например password_reset_token)
        $user->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
        $user->save(false);

        return Yii::$app->mailer->compose()
            ->setTo($this->email)
            ->setSubject('Восстановление пароля')
            ->setTextBody("Перейдите по ссылке для восстановления пароля: " .
                Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]))
            ->send();
    }
}
