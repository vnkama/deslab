<!doctype html>
<html>

<head lang="ru">
    <meta charset="utf-8">
    <title><?= ($toView['title']) ?? ''  ?></title>

    <link rel='stylesheet' href='css/styles.css'>
    <script src='js/jquery-3.4.1.js'></script>
    <script src="js/scripts.js"></script>

    <?php if(isset($toView['script_file'])) { ?> <script src="js/<?= $toView['script_file'] ?>"></script> <?php } ?>

    <!--фансибокс-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
    <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script
</head>


<!--BODY-->
<?php echo (isset($toView['body_class'])) ? "<body class='${toView['body_class']}'>" : '<body>' ?>

<!--вся начинка body-->

<?php (!isset($toView['body_file'])) ?: include($toView['body_file']); ?>

</body>

</html>
