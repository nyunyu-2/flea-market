<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>取引完了のお知らせ</title>
</head>
<body>
    <p>{{ $purchase->item->user->username }}さん、</p>
    <p>商品「{{ $purchase->item->name }}」の取引が購入者により完了されました。</p>
    <p>購入者からの評価はマイページで確認できます。</p>
    <p>ありがとうございました。</p>
</body>
</html>
