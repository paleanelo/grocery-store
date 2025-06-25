<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\httpclient\Client;
use app\models\Product;
use app\models\ProductImage;
use app\models\Category;

class VkParserController extends Controller
{
    public $enableCsrfValidation = false;
    private $groupId = '230770785';
    private $apiVersion = '5.199';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['parse'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return Yii::$app->user->identity->role_id === 2;
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'parse' => ['GET'],
                ],
            ],
        ];
    }

    public function actionParse()
    {
        $tokenPath = Yii::getAlias('@app/config/vk-token.php');

        if (!file_exists($tokenPath)) {
            Yii::$app->session->setFlash('error', 'Файл с токеном не найден.');
            return $this->redirect(['admin/products']);
        }

        $tokenData = require $tokenPath;
        $token = $tokenData['vkUserToken'] ?? null;

        if (!$token) {
            Yii::$app->session->setFlash('error', 'Токен не указан.');
            return $this->redirect(['admin/products']);
        }

        $client = new Client();

        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('https://api.vk.com/method/market.get')
            ->setData([
                'owner_id' => '-' . $this->groupId,
                'access_token' => $token,
                'v' => $this->apiVersion,
                'count' => 100
            ])
            ->send();

        if (!$response->isOk || !isset($response->data['response']['items'])) {
            $error = $response->data['error']['error_msg'] ?? 'Ошибка запроса к VK.';
            Yii::$app->session->setFlash('error', "Ошибка при получении данных из VK: {$error}");
            return $this->redirect(['admin/products']);
        }

        $items = $response->data['response']['items'];
        $added = 0;
        $skipped = 0;

        foreach ($items as $item) {
            $title = $item['title'];

            if (Product::find()->where(['name' => $title])->exists()) {
                $skipped++;
                continue;
            }

            $categoryName = $this->detectCategory($title);
            $category = Category::findOne(['name' => $categoryName]);

            if (!$category) {
                $category = new Category();
                $category->name = $categoryName;
                $category->slug = strtolower(trim(preg_replace('/[^a-zA-Zа-яА-Я0-9]+/u', '-', $categoryName), '-'));
                $category->save(false);
            }

            $price = isset($item['price']['amount']) ? $item['price']['amount'] / 100 : null;
            $pricePerBox = $price > 2000 ? $price : null;
            $pricePerKg = $price <= 2000 ? $price : null;

            $product = new Product();
            $product->name = $title;
            $product->description = $item['description'] ?? null;
            $product->price_per_box = $pricePerBox;
            $product->price_per_kg = $pricePerKg;
            $product->avg_weight = null;
            $product->is_active = 1;
            $product->expire_days = null;
            $product->category_id = $category->id;

            if ($product->save()) {
                if (!empty($item['thumb_photo'])) {
                    $image = new ProductImage();
                    $image->product_id = $product->id;
                    $image->image_path = $item['thumb_photo'];
                    $image->is_main = 1;
                    $image->save(false);
                }
                $added++;
            }
        }

        Yii::$app->session->setFlash('success', "Загружено товаров: {$added}, пропущено (дубликаты): {$skipped}");
        return $this->redirect(['admin/products']);
    }

    private function detectCategory($title)
    {
        $title = mb_strtolower($title);

        if (strpos($title, 'фрукт') !== false) return 'Фрукты';
        if (strpos($title, 'овощ') !== false) return 'Овощи';
        if (strpos($title, 'ягод') !== false) return 'Ягоды';
        if (strpos($title, 'сухофрукт') !== false) return 'Сухофрукты';
        if (strpos($title, 'орех') !== false) return 'Орехи';

        return 'Прочее';
    }
}
