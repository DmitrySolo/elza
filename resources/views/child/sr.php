<?
    $srt['advert']='Реклама';
    $srt['search']='Поиск';
    $styled_sts = "style='background-color:green;color:#fff;'";

?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Регион</th><th>Город</th><th>Запрос</th><th>G</th><th>Я</th>
        </tr>
    </thead>
    <?//dd($data)?>
        <?php foreach($data['result'] as $regionName => $regionData):?>
            <tr class="danger"><td><?=$regionName?></td><td></td><td></td><th></th><th></th></tr>
            <?php foreach($regionData as $cityName => $query):?>
                <tr class="warning"><td></td><td><?=$cityName?></td><td></td><th></th><th></th></tr>
                <?php foreach($query as $queryName => $queryData):?>
                <tr class="success"><td></td><td></td><td><?=$queryName?></td><th></th><th></th></tr>
                    <?php foreach($queryData->google as $searchType => $searchResult):?>
                        <tr><td></td><td></td><td></td><td>Google: <?=$srt[$searchType]?></td><td></td></tr>
                        <tr><td></td><td></td><td></td><td>
                                <ol>
                                <?php foreach ($searchResult as $key => $value):?>
                                    <li><?=$value?></li>
                                <?php endforeach;?>
                                </ol>
                            </td><td></td></tr>
                    <?php endforeach; ?>
                    <?php foreach($queryData->yandex as $searchType => $searchResult):?>
                        <tr><td></td><td></td><td></td><td>Yandex: <?=$srt[$searchType]?></td><td></td></tr>
                        <tr><td></td><td></td><td></td><td>
                                <ol>
                                    <?php foreach ($searchResult as $key => $value):?>
                                        <li <?php if($value =='santehsmart.ru') echo $styled_sts?> ><?=$value?></li>
                                    <?php endforeach;?>
                                </ol>
                            </td><td></td></tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>
</table>