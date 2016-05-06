<div class="form-group package" data-package="<?=$package?>">
    <div class="col-xs-4">
        <label><input type="text" class="form-control" placeholder="Вес комплекта" name="PACKAGES[<?=$package?>][weight]"></label>
    </div>
    <div class="col-xs-8">
        <label>Габариты:<input type="text" class="form-control" name="PACKAGES[<?=$package?>][size_a]">
            <input type="text" class="form-control" name="PACKAGES[<?=$package?>][size_b]">
            <input type="text" class="form-control" name="PACKAGES[<?=$package?>][size_c]"></label>
    </div>
</div>
<div class="form-group">
    <div class="col-xs-4">
        <a class="product-add" href="#" data-package="<?=$package?>" data-item="0">Добавить товар</a>
    </div>
</div>
<div class="form-group">
    <div class="col-xs-4">
        <a class="package-add" href="#" data-package="<?=($package+1)?>">Добавить комплект</a>
    </div>
</div>
