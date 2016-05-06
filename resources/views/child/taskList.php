    <table class="table table-striped">
        <thead> <tr> <th>#</th> <th>Индекс</th> <th>Название</th> <th>Status</th><th></th> </tr> </thead>
<?php foreach($data as $task):?>
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
    <iframe src="http://translate.google.com/translate_tts?tl=RU&q=Добрый  День Уважаемые Директора,   как ваше душевное настроение   чем могу быть полезна&output=embed" width="0" height="0" border="0"></iframe>