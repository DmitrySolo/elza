<label>
    <select name="pvz" class="form-control">
    <option value="">Выберите пункт выдачи</option>
    <?foreach($data as $pvz):?>
    <option value="<?=$pvz['Code']?>"><?=$pvz['Name']?></option>
    <?endforeach;?>
    </select>
</label>