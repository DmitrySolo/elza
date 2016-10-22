<div class="form-group package" data-package="<?=$package?>">
    <div class="col-xs-4">
        <label><span>Вес</span><input type="text" class="form-control" placeholder="Вес комплекта" name="PACKAGES[<?=$package?>][weight]"></label>
    </div>
    <div class="col-xs-1">
        <label>Габариты:</label>
    </div>
    <div class="col-xs-7">
        <label><span>Длина</span><input type="text" class="form-control" name="PACKAGES[<?=$package?>][size_a]"></label>
        <label><span>Ширина</span><input type="text" class="form-control" name="PACKAGES[<?=$package?>][size_b]"></label>
        <label><span>Высота</span><input type="text" class="form-control" name="PACKAGES[<?=$package?>][size_c]"></label>
        <a class="package-del" href="#" data-package="<?=$package?>">X</a>
    </div>
</div>
<div class="form-group">
    <div class="col-xs-4">
        <a class="product-add" href="#" data-package="<?=$package?>" data-item="0">Добавить товар</a>
    </div>
</div>
<div class="form-group">
    <div class="col-xs-4">
        <a class="package-add" href="#" data-package="<?=($package+1)?>">Добавить место</a>
    </div>
</div>
