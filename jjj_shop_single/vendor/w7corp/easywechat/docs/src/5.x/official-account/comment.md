# 評論資料管理



## 開啟已群發文章評論

```php
$app->comment->open($msgId, $index = null);
```

## 關閉已群發文章評論

```php
$app->comment->close($msgId, $index = null);
```

## 檢視指定文章的評論資料

```php
$app->comment->list(string $msgId, int $index, int $begin, int $count, int $type = 0);
```

## 將評論標記精選

```php
$app->comment->markElect(string $msgId, int $index, int $commentId);
```

## 將評論取消精選

```php
$app->comment->unmarkElect(string $msgId, int $index, int $commentId);
```

## 刪除評論

```php
$app->comment->delete(string $msgId, int $index, int $commentId);
```

## 回覆評論

```php
$app->comment->reply(string $msgId, int $index, int $commentId, string $content);
```

## 刪除回覆

```php
$app->comment->deleteReply(string $msgId, int $index, int $commentId);
```
