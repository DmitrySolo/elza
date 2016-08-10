<style>
    .task-info-wrp section{
        float: left;
        padding: 10px;
    }
    .task-info-wrp h3{
        width: 100%;
        text-align: center;
    }
    .task-info{
        width: 65%;
        background-color: #FFFFEA;
    }
    .client-info{
        width: 35%;
        background-color: #DCE8DC;
    }
</style>




<div class="task-info-wrp clearfix">
    <h3><? echo $data->task_name; ?></h3>
    <section class="task-info">
       <p class="task-status"><? echo 'Статус: '.$data->step_description; ?></p>
        <p>Находиться в ожидании: <? echo $data->waiting." минут"; ?><small>(Добавлена:<? echo $data->at_; ?>)</small></p>
        <p>Последнее действие: <? echo $data->step_reason; ?></p>
        <p class="task-problem">Описание проблемы: <? echo $data->description; ?></p>
        <?if(isset($data->document_id)){?>
        <p>Проблема поступила по накладной:  <a class="showALLbyRDS" href="#allAboutRDS" data-rds="<? echo $data->document_id; ?>"><? echo $data->document_id; ?></a></p>
        <?}?>
        <div>

            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Отложить задачу</a></li>
                <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Перевести статус</a></li>
                <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">Завершить задачу</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="home">

                    <form data-toggle="validator" id="TaskForm">
                        <input type="hidden" value="<?php echo $data->waiting ?>" name="waiting" required>
                        <input type="hidden" value="<?php echo $data->id?>" name="id" id="taskId" required>
                        <input type="hidden" value="<?php echo $data->step_description?>" name="status" required>
                        <input type="hidden" value="<?php echo $data->step_count?>" name="step_count" required>
                        <p>Причина переноса задачи:(Будет отправлено КЛИЕНТУ ВНИМАНИЕ!)</p>
                        <textarea class="form-control" rows="3" name="DelayReasonDesc" data-minlength="5" required></textarea>
                        <p>Необходимое время:(Будет отправлено КЛИЕНТУ ВНИМАНИЕ!)</p>
                        <select class="form-control" name="setTime">
                            <option value="30">30 минут</option>
                            <option value="60">1 часа</option>
                            <option value="120">2 часов</option>
                            <option value="180">3 часов</option>
                            <option value="240">4 часов</option>
                            <option value="300">5 часов</option>
                            <option value="360">6 часов</option>
                            <option value="480">В течении 24 часов</option>
                            <option value="960">В течении 48 часов</option>
                            <option value="1520">В течении 3х рабочих дней</option>
                            <option value="1920">В течении 4 рабочих дней</option>
                            <option value="2400">В течении 5 рабочих дней</option>
                        </select>
                    </form>
                    <div><button  class="validateAndSend" data-form="TaskForm" data-url="/ajax/delayTask" data-content=".validateAndSend" data-dismiss="modal">ОК</button></div>
                </div>
                <div role="tabpanel" class="tab-pane" id="profile">
                    <form id="changeStatus">
                        <input type="hidden" value="<?php echo $data->step_count?>" name="step_count" required>
                        <input type="hidden" value="<?php echo $data->id?>" name="id" id="taskId" required>
                        <input type="hidden" value="<?php echo $data->waiting ?>" name="waiting" required>
                        <input type="hidden" value="<?php echo $data->task_type ?>" name="task_type" required>
                        <p>Действие изменившее статус задачи:</p>
                        <textarea name="step_reason" required></textarea>
                        <p>Новый статус задачи:(Будет отправлено КЛИЕНТУ ВНИМАНИЕ!)</p>
                        <?if($data->task_type==1){?>
                            <input type="hidden" value="<?php echo $data->order_id ?>" name="order_id" required>
                            <select class="form-control" name="status" required>
                                <?foreach($statuses['OPEN'] as $status){?>
                                    <option value="<?=$status['ID']?>"><?=$status['NAME']?></option>
                                <?}?>
                            </select>
                        <?}else{?>
                        <textarea name="status" required></textarea>
                        <?}?>

                    <select name="setTime" required>
                        <option value="30">30 минут</option>
                        <option value="60">1 часа</option>
                        <option value="120">2 часов</option>
                        <option value="180">3 часов</option>
                        <option value="240">4 часов</option>
                        <option value="300">5 часов</option>
                        <option value="360">6 часов</option>
                        <option value="480">В течении 24 часов</option>
                        <option value="960">В течении 48 часов</option>
                        <option value="1520">В течении 3х рабочих дней</option>
                        <option value="1920">В течении 4 рабочих дней</option>
                        <option value="2400">В течении 5 рабочих дней</option>
                    </select>
                    </form>
                    <div><button  class="validateAndSend" data-form="changeStatus" data-url="/ajax/changeTaskStatus" data-content=".validateAndSend" data-dismiss="modal">ОК</button></div>

                </div>
                <div role="tabpanel" class="tab-pane" id="messages">
                    <form id="DoneTask">
                        <input type="hidden" value="<?php echo $data->step_count?>" name="step_count" required>
                        <input type="hidden" value="<?php echo $data->id?>" name="id" id="taskId" required>
                        <input type="hidden" value="<?php echo $data->waiting ?>" name="waiting" required>
                        <div>
                            <?if($data->task_type==1){?>
                                <input type="hidden" value="<?php echo $data->order_id ?>" name="order_id" required>
                                <select class="form-control" name="DoneTask" required>
                                    <?foreach($statuses['CLOSE'] as $status){?>
                                        <option value="<?=$status['ID']?>"><?=$status['NAME']?></option>
                                    <?}?>
                                </select>
                            <?}else{?>
                                <select class="form-control" name="DoneTask" required>
                                    <option value="Клиент отозвал заявку">Клиент отозвал заявку</option>
                                    <option value="Отказ в удовлетворении">Отказ в удовлетворении</option>
                                    <option value="Клиенту были возвращены средства">Клиенту были возвращены средства</option>
                                    <option value="Клиенту была произведена замена">Клиенту была произведена замена</option>
                                </select>
                            <?}?>
                        </div>
                    </form>
                    <div><button  class="validateAndSend" data-form="DoneTask" data-url="/ajax/DoneTask" data-content=".validateAndSend" data-dismiss="modal">ОК</button></div>
                </div>
            </div>

        </div>
    </section>
    <section class="client-info">
        <?if(!empty($client)){?>
        <h4>Клиент:</h4>
        <p><?php echo $client->name; ?></p>
        <p><?php echo $client->address; ?></p>
        <p><?php echo $client->phone; ?></p>
        <p><?php echo $client->city; ?></p>
        <?}?>
        <?if(isset($data->order_id)){?>
            <h4>Номер заказа:</h4>
            <?php echo $data->order_id ?>
        <?}?>
        <?if(isset($allDoc)){?>
        <h4>История заказов:</h4>
        <?php
            foreach ($allDoc as $doc){
                echo $doc->number;
            }
        ?>
        <?}?>
        <?if(isset($appealHis)){?>
        <h4>История обращений:</h4>
        <?php foreach($appealHis as $appeal=>$desc){
            echo $appeal.' <small>'.$desc.'</small><br>';
        }?>
        <?}?>
    </section>
</div>


<div id="allAboutRDS"></div>
