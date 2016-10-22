<div class="form-group product" data-package="<?=$package?>" data-item="<?=$item?>">
    <div class="col-xs-12">
        <label><input type="text" style="width: 70px" class="form-control" placeholder="Артикул товара" name="PACKAGES[<?=$package?>][Items][<?=$item?>][sku]"></label>
        <label><input type="text" style="width: 600px" class="form-control" placeholder="Наименование товара" name="PACKAGES[<?=$package?>][Items][<?=$item?>][name]"></label>
        <label><input type="text" style="width: 60px" class="form-control" placeholder="Кол-во" value="1" name="PACKAGES[<?=$package?>][Items][<?=$item?>][amount]"></label>
        <label><input type="text" style="width: 60px" class="form-control" placeholder="Цена" name="PACKAGES[<?=$package?>][Items][<?=$item?>][price]"></label>
        <label><input type="text" style="width: 170px" class="form-control" placeholder="Оплата при получении" name="PACKAGES[<?=$package?>][Items][<?=$item?>][payment]"></label>
        <label><input type="text" style="width: 50px" class="form-control" placeholder="Вес" name="PACKAGES[<?=$package?>][Items][<?=$item?>][weight]"></label>
    </div>
</div>
<div class="form-group">
    <div class="col-xs-4">
        <a class="product-add" href="#" data-package="<?=$package?>" data-item="<?=($item+1)?>">Добавить товар</a>
    </div>
</div>
