# 產品圖冊

### 建立商品圖冊

```php
$params = [
  'description'=>'世界上最好的商品',
  'price'=>30000,
  'product_sn'=>'xxxxxxxx',
  'attachments'=>[
      [
          'type'=> 'image',
          'image'=> [
              'media_id'=> 'MEDIA_ID'
          ]
      ]
  ]
];

$app->product->createProductAlbum($params);
```

### 獲取商品圖冊列表

```php
$app->product->getProductAlbums(int $limit, string $cursor);
```

### 獲取商品圖冊

```php
$productId = 'productId';

$app->product->getProductAlbumDetails($productId);
```

### 刪除商品圖冊

```php
$productId = 'productId';

$app->product->deleteProductAlbum($productId);
```
