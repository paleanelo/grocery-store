<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $full_name
 * @property string|null $auth_key
 * @property int|null $vk_id
 * @property int $role_id
 *
 * @property Cart[] $carts
 * @property Role $role
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    const SCENARIO_UPDATE = 'update'; 
    public $agree;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['auth_key', 'vk_id'], 'default', 'value' => null],
            [['role_id'], 'default', 'value' => 1],
            [['username', 'email', 'full_name'], 'required', 'message' => 'Обязательное поле'],
            [['vk_id', 'role_id'], 'integer'],
            [['username', 'email', 'password', 'full_name', 'password_reset_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            ['password', 'required', 'on' => self::SCENARIO_DEFAULT, 'message' => 'Обязательное поле'], // Только при регистрации
            ['password', 'safe', 'on' => self::SCENARIO_UPDATE], // При обновлении - безопасное поле
            [['username'], 'unique', 'message' => 'Этот логин уже занят.'],
            [['email'], 'unique', 'message' => 'Аккаунт с этой почтой уже существует.'],
            ['email', 'email', 'message' => 'Введите корректный email'],
            ['password', 'string', 'tooShort' => 'Пароль должен содержать минимум 6 символов', 'min' => 6],
            ['agree', 'boolean'],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::class, 'targetAttribute' => ['role_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
            'full_name' => 'ФИО',
            'auth_key' => 'Auth Key',
            'vk_id' => 'Vk ID',
            'role_id' => 'Role ID',
            'agree' => '',
        ];
    }

    /**
     * Gets query for [[Carts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarts()
    {
        return $this->hasMany(Cart::class, ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Role]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::class, ['id' => 'role_id']);
    }

    public function register()
    {
        if ($this->validate()) {
            $this->password = Yii::$app->security->generatePasswordHash($this->password);
            $this->auth_key = Yii::$app->security->generateRandomString();
            return $this->save(false);
        }
        return false;
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null; // Не используем токены
    }

    public function getId()
    {
        return $this->id;
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }
}
