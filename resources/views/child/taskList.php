<ul class="nav nav-tabs" role="tablist">
<?php foreach($data as $tab_name=>$tab):?>
    <li role="presentation" <?=$tab_name=='open'?'class="active"':''?>>
        <a href="#<?=$tab_name?>" aria-controls="<?=$tab_name?>" role="tab" data-toggle="tab"><?=$tab_name?></a>
    </li>
<?php endforeach;?>
</ul>
<div class="tab-content">
<?php foreach($data as $tab_name=>$tab):?>
<div role="tabpanel" class="tab-pane <?=$tab_name=='open'?'active':''?>" id="<?=$tab_name?>">
<table class="table table-striped">
    <thead> <tr> <th>#</th> <th>Индекс</th> <th>Название</th> <th>Status</th><th></th> </tr> </thead>
<?php foreach($tab as $task):?>
    <tr>
    <td><?php echo $task->id;?></td>
        <td>
            <?php echo $task->priority_index;?>
        </td>
        <td><?php echo $task->task_name.' <small style="color:orangered">('.$task->step_description.')</small>';?></td>
        <?php if($task->priority_index < 1):?>
            <?php echo $mark=($task->priority_index < 0.5)?"<td><span class='btn btn-xs btn-danger'>Danger!</span></td>"
                                                        :"<td><span class='btn btn-xs btn-warning'>Warning!</span></td>"
            ;?>
        <?php else:
            echo "<td><span class='btn btn-xs btn-success'>Succes!</span></td>"
        ?>
        <?php endif ?>
        <td><button class='btn btn-sm btn-primary taskMore' data-id="<?php echo $task->id;?>" data-type="<?php echo $task->task_type_id; ?>" data-toggle="modal" data-target="#taskModal">Подробнее</button></td>
    </tr>
<?php endforeach;?>
</table>
</div>
<?php endforeach;?>
</div>
