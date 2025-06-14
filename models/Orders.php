<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property int $user_id
 * @property string $customer_name
 * @property string $customer_phone
 * @property string $customer_address
 * @property int $status_id
 * @property float|null $total_price
 * @property string $created_at
 *
 * @property OrdersItem[] $ordersItems
 * @property Status $status
 * @property User $user
 */
class Orders extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['total_price'], 'default', 'value' => null],
            [['status_id'], 'default', 'value' => 1],
            [['user_id', 'customer_name', 'customer_phone', 'customer_address'], 'required'],
            [['user_id', 'status_id'], 'integer'],
            [['total_price'], 'number'],
            [['created_at'], 'safe'],
            [['customer_name', 'customer_address'], 'string', 'max' => 255],
            [['customer_phone'], 'string', 'max' => 50],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => Status::class, 'targetAttribute' => ['status_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'customer_name' => 'Customer Name',
            'customer_phone' => 'Customer Phone',
            'customer_address' => 'Customer Address',
            'status_id' => 'Status ID',
            'total_price' => 'Total Price',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[OrdersItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(OrdersItem::class, ['orders_id' => 'id']);
    }

    /**
     * Gets query for [[Status]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(Status::class, ['id' => 'status_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

}
