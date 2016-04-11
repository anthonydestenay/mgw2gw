<?php

  require_once 'bootstrap.php';

  function get_header() {
    if(isset($_GET['step']) && !empty($_GET['step'])) {
      $step = (int) $_GET['step'];
    }
    require_once __DIR__.'/install/_header.php';
    return;
  }

  function get_footer() {
    require_once __DIR__.'/install/_footer.php';
    return;
  }

    if(isset($_GET['step']) && !empty($_GET['step'])) {
      $step = (int) $_GET['step'];
      require_once __DIR__."/install/step-$step.php";
    } else {
      header('Location: ?step=1');
      exit();
    }

?>
