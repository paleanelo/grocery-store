<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Product;

/**
 * ProductSearch represents the model behind the search form of `app\models\Product`.
 */
class ProductSearch extends Product
{
    public $categoryName;
    public $sort;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'is_active', 'expire_days'], 'integer'],
            [['name', 'slug', 'description', 'created_at', 'updated_at'], 'safe'],
            [['price_per_kg', 'price_per_box', 'avg_weight'], 'number'],
            [['categoryName', 'sort'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }


    public function search($params, $formName = null)
    {
        $query = Product::find()->joinWith('category');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 12,
            ],
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Фильтрация по активности
        if ($this->is_active === null) {
            $query->andWhere(['product.is_active' => 1]);
        } else {
            $query->andFilterWhere(['product.is_active' => $this->is_active]);
        }

        $query->andFilterWhere(['like', 'product.name', $this->name]);
        $query->andFilterWhere(['like', 'category.name', $this->categoryName]);

        $query->andFilterWhere([
            'product.id' => $this->id,
            'product.category_id' => $this->category_id,
            'product.price_per_kg' => $this->price_per_kg,
            'product.price_per_box' => $this->price_per_box,
            'product.avg_weight' => $this->avg_weight,
            'product.expire_days' => $this->expire_days,
        ]);

        if (!empty($this->created_at)) {
            $query->andFilterWhere(['like', 'product.created_at', $this->created_at]);
        }

        if (!empty($this->updated_at)) {
            $query->andFilterWhere(['like', 'product.updated_at', $this->updated_at]);
        }

        $query->andFilterWhere(['like', 'product.slug', $this->slug])
            ->andFilterWhere(['like', 'product.description', $this->description]);

        // ✅ Оставляем кастомную сортировку только если явно указана через поле sort (твоё дополнительное поле)
        if ($this->sort === 'price_asc') {
            $query->orderBy([
                new \yii\db\Expression('COALESCE(NULLIF(product.price_per_kg, 0), product.price_per_box) ASC')
            ]);
        } elseif ($this->sort === 'price_desc') {
            $query->orderBy([
                new \yii\db\Expression('COALESCE(NULLIF(product.price_per_kg, 0), product.price_per_box) DESC')
            ]);
        }

        return $dataProvider;
    }
}
