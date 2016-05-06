<div class="form-group product" data-package="<?=$package?>" data-item="<?=$item?>">
    <div class="col-xs-2">
        <label><input type="text" class="form-control" placeholder="Артикул товара" name="PACKAGES[<?=$package?>][Items][<?=$item?>][sku]"></label>
    </div>
    <div class="col-xs-5">
        <label><input type="text" class="form-control long" placeholder="Наименование товара" name="PACKAGES[<?=$package?>][Items][<?=$item?>][name]"></label>
    </div>
    <div class="col-xs-1">
        <label><input type="text" class="form-control" placeholder="Кол-во" value="1" name="PACKAGES[<?=$package?>][Items][<?=$item?>][amount]"></label>
    </div>
    <div class="col-xs-1">
        <label><input type="text" class="form-control" placeholder="Цена" name="PACKAGES[<?=$package?>][Items][<?=$item?>][price]"></label>
    </div>
    <div class="col-xs-2">
        <label><input type="text" class="form-control" placeholder="Оплата при получении" name="PACKAGES[<?=$package?>][Items][<?=$item?>][payment]"></label>
    </div>
    <div class="col-xs-1">
        <label><input type="text" class="form-control" placeholder="Вес" name="PACKAGES[<?=$package?>][Items][<?=$item?>][weight]"></label>
    </div>
</div>
<div class="form-group">
    <div class="col-xs-4">
        <a class="product-add" href="#" data-package="<?=$package?>" data-item="<?=($item+1)?>">Добавить товар</a>
    </div>
</div>
