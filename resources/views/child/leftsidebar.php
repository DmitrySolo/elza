<div <?php $class=(Request::is('/'))?"shield-selected":"";?> class="shield <? echo $class;?>  task-shield"><h4><a href="/">Задачи</a></h4></div>
<div <?php $class=(Request::is('rds'))?"shield-selected":"";?> class="shield <? echo $class;?> task-shield"><h4><a href="/rds">РДС</a></h4></div>
<div <?php $class=(Request::is('bitrix'))?"shield-selected":"";?> class="shield <? echo $class;?>  task-shield"><h4><a href="/bitrix">Заказы</a></h4></div>
<div <?php $class=(Request::is('search_rating'))?"shield-selected":"";?> class="shield <? echo $class;?>  task-shield"><h4><a href="/search_rating">Анализ присутствия</a></h4></div>
<div <?php $class=(Request::is('stats'))?"shield-selected":"";?> class="shield <? echo $class;?>  task-shield"><h4><a href="/stats">Анализ продаж</a></h4></div>