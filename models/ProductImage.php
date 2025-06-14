<?php

namespace app\models;
use yii\web\UploadedFile;
use yii\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "product_image".
 *
 * @property int $id
 * @property int $product_id
 * @property string $image_path
 * @property int|null $is_main
 *
 * @property Product $product
 */
class ProductImage extends \yii\db\ActiveRecord
{
    public $imageFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_main'], 'default', 'value' => 0],
            [['product_id'], 'required'],
            [['image_path'], 'safe'],
            [['product_id', 'is_main'], 'integer'],
            [['image_path'], 'string', 'max' => 255],
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, jpeg, gif'],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['product_id' => 'id']],
    
            // Кастомное правило — не больше 5 фото
            ['product_id', 'validatePhotoLimit'],
        ];
    }
    

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'image_path' => 'Image Path',
            'is_main' => 'Is Main',
        ];
    }

    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }



    public function uploadAndSave()
    {
        if ($this->validate()) {
            if ($this->imageFile) {
                $product = Product::findOne($this->product_id);
                if (!$product) {
                    return false;
                }
    
                $folderName = $this->transliterate($product->name);
                $dir = Yii::getAlias('@webroot/img/' . $folderName);
    
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
    
                $baseName = $this->transliterate($this->imageFile->baseName);
                $fileName = $baseName . '.' . $this->imageFile->extension;
                $filePath = $dir . '/' . $fileName;
    
                // Проверка на уникальность имени файла
                $counter = 1;
                while (file_exists($filePath)) {
                    $fileName = $baseName . '_' . $counter . '.' . $this->imageFile->extension;
                    $filePath = $dir . '/' . $fileName;
                    $counter++;
                }
    
                if ($this->imageFile->saveAs($filePath)) {
                    $this->image_path = 'img/' . $folderName . '/' . $fileName;
    
                    // Убедимся, что is_main = 0 или 1
                    if (!in_array($this->is_main, [0, 1])) {
                        $this->is_main = 0;
                    }
    
                    return $this->save(false); // сохраняем модель product_image
                }
            }
        }
    
        return false;
    }       
    
    private function transliterate($text)
    {
        $translit = [
            'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'yo','ж'=>'zh',
            'з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o',
            'п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'ts',
            'ч'=>'ch','ш'=>'sh','щ'=>'sch','ъ'=>'','ы'=>'y','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya',
            'А'=>'A','Б'=>'B','В'=>'V','Г'=>'G','Д'=>'D','Е'=>'E','Ё'=>'Yo','Ж'=>'Zh',
            'З'=>'Z','И'=>'I','Й'=>'Y','К'=>'K','Л'=>'L','М'=>'M','Н'=>'N','О'=>'O',
            'П'=>'P','Р'=>'R','С'=>'S','Т'=>'T','У'=>'U','Ф'=>'F','Х'=>'H','Ц'=>'Ts',
            'Ч'=>'Ch','Ш'=>'Sh','Щ'=>'Sch','Ъ'=>'','Ы'=>'Y','Ь'=>'','Э'=>'E','Ю'=>'Yu','Я'=>'Ya'
        ];
        return strtr($text, $translit);
    }

    public function validatePhotoLimit($attribute, $params)
    {
        if (!$this->product_id) {
            return;
        }

        $existingCount = ProductImage::find()
            ->where(['product_id' => $this->product_id])
            ->count();

        // Если создаётся новая запись или клонируется
        if ($this->isNewRecord && $existingCount >= 5) {
            $this->addError($attribute, 'Нельзя добавить больше 5 изображений к одному товару.');
        }
    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        // Абсолютный путь к файлу
        $filePath = Yii::getAlias('@webroot/' . $this->image_path);

        if (file_exists($filePath)) {
            @unlink($filePath); // удаляем файл, @ подавляет предупреждение, если файла нет
        }

        return true;
    }
}
