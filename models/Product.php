<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string|null $slug
 * @property float|null $price_per_kg
 * @property float|null $price_per_box
 * @property float|null $avg_weight
 * @property string|null $description
 * @property int|null $is_active
 * @property int|null $expire_days
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property CartItem[] $cartItems
 * @property Category $category
 * @property ProductImage[] $productImages
 */
class Product extends \yii\db\ActiveRecord
{

    public $mainImageFile;
    public $extraImageFiles; // массив


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['slug', 'price_per_kg', 'price_per_box', 'avg_weight', 'description', 'expire_days'], 'default', 'value' => null],
            [['is_active'], 'default', 'value' => 1],

            [['category_id', 'name'], 'required', 'message' => 'Обязательное поле'],

            [['category_id', 'is_active', 'expire_days'], 'integer', 'message' => 'Допустимы только целые числа'],
            [['expire_days'], 'match', 'pattern' => '/^\d+$/', 'message' => 'Допустимы только цифры'],

            [['price_per_kg', 'price_per_box', 'avg_weight'], 'match', 'pattern' => '/^\d+(\.\d{1,2})?$/', 'message' => 'Допустимы только цифры и точка'],
            [['price_per_kg', 'price_per_box', 'avg_weight'], 'number', 'message' => 'Значение должно быть числом'],

            [['description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],

            [['name', 'slug'], 'string', 'max' => 255],
            [['name'], 'match', 'pattern' => '/^[А-Яа-яЁё\s\-]+$/u', 'message' => 'Допустимы только кириллические символы и пробелы'],

            [['slug'], 'unique', 'message' => 'Этот URL уже используется'],

            [['mainImageFile'], 'file', 'extensions' => 'png, jpg, jpeg', 'skipOnEmpty' => true],
            [['extraImageFiles'], 'file', 'extensions' => 'png, jpg, jpeg', 'maxFiles' => 4, 'skipOnEmpty' => true, 'tooMany' => 'Можно загрузить не более 4 файлов.'],

            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Category ID',
            'name' => 'Name',
            'slug' => 'Slug',
            'price_per_kg' => 'Price Per Kg',
            'price_per_box' => 'Price Per Box',
            'avg_weight' => 'Avg Weight',
            'description' => 'Description',
            'is_active' => 'Is Active',
            'expire_days' => 'Expire Days',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[CartItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCartItems()
    {
        return $this->hasMany(CartItem::class, ['product_id' => 'id']);
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[ProductImages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(ProductImage::class, ['product_id' => 'id']);
    }

    public function getMainImage()
    {
        return $this->hasOne(ProductImage::class, ['product_id' => 'id'])->andWhere(['is_main' => 1]);
    }
    // Все изображения
    public function getProductImages()
    {
        return $this->hasMany(ProductImage::class, ['product_id' => 'id']);
    }



    public static function deactivateExpired()
    {
        $now = time();

        $products = self::find()
            ->where(['is_active' => 1])
            ->andWhere(['>', 'expire_days', 0])
            ->all();

        foreach ($products as $product) {
            $baseTime = strtotime($product->updated_at); // используем updated_at вместо created_at
            $expireSeconds = (int)$product->expire_days * 86400;

            if ($baseTime + $expireSeconds <= $now) {
                $product->is_active = 0;
                $product->save(false);
            }
        }
    }
}
