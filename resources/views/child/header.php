<a class="title" href="/">ELZA</a>
<?php
if (Auth::check()) {
    echo Auth::user()->name;
    echo "<a href=\"/logout\"><button type=\"button\" class=\"btn btn-danger\">Выйти</button></a>";
}else{
   echo "Ты не авторизован";
}
?>