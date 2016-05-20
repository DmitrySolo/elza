<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="/css/bootstrap-theme.min.css">
        <link rel="stylesheet" type="text/css" href="/css/style.css">
        <link rel="stylesheet" type="text/css" href="/jquery-ui/jquery-ui.min.css">
        <link rel="stylesheet" type="text/css" href="/jquery-ui/jquery-ui.structure.min.css">
        <link rel="stylesheet" type="text/css" href="/jquery-ui/jquery-ui.theme.min.css">
    </head>
    <body class="elza">
    <header>
        <div class="row">
            <div class="col-xs-12">
                <?php echo $header; ?>
            </div>
        </div>
    </header>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-1 leftsidebar">
                <section class=""><?php echo $leftsidebar; ?></section>
            </div>
            <div class="col-xs-8 col-lg-offset-1">
                <main class="main"><?php echo $main; ?></main>
            </div>
            <div class="col-xs-3 rightsidebar col-lg-offset-9">
                <section class=""><?php echo $rightsidebar; ?></section>
            </div>
        </div>
    </div>

    <!-- Modal -->
    @include('modals.returnModal')
    @include('modals.problemModal')
    @include('modals.taskModal')
    @include('modals.rdsModal')
    @include('modals.newCDEKModal')
    @include('modals.bitrixModal')
    @include('modals.taskInfoModal')

    <script src="/js/jquery-1.12.1.min.js"></script>
    <script src="/jquery-ui/jquery-ui.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/script.js"></script>
    <script src="jquery.tablesorter.min.js"></script>
    <script src="/js/bootstrap-validator-master/js/validator.js"></script>
    <script src="/js/validatorBySOL.js"></script>
    <footer class="footer navbar-fixed-bottom row-fluid"><?php echo $footer; ?></footer>
    </body>
</html>