<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Оформление возврата в очередь</h4>
            </div>
            <div class="modal-body">
                <form class="form-inline" method="post" action="/return">
                    <h4>Поиск информации по номеру возврата</h4>
                    <label>Номер возврата <input class="form-control" type="text" name="Number"></label>
                    <label>Дата начала<input class="form-control" type="date" name="DateFirst" value="2016-02-17"></label>
                    <label>Дата окончания <input class="form-control" type="date" name="DateLast" value="2016-03-02"></label>
                    <input class="btn btn-default" type="submit" value="ОК">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </div>
</div>