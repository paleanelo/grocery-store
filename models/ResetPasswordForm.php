<?php
namespace app\models;

use yii\base\Model;

class ResetPasswordForm extends Model
{
    public $password;

    public function rules()
    {
        return [['password', 'required', 'message' => 'Заполните поле'], ['password', 'string', 'tooShort' => 'Пароль должен содержать минимум 6 символов', 'min' => 6]];
    }
}
