<?php

  if($_POST) {

    if( isset($_POST['config']['api']['key']) && !empty($_POST['config']['api']['key']) ) {

      try {
        $api = new \GW2Treasures\GW2Api\GW2Api();
        $api->account(trim($_POST['config']['api']['key']))->get();

        $config_file = __DIR__.'/../config.yaml';

        $data = array_merge($config, $_POST['config']);
        $yaml = Spyc::YAMLDump($data);
        $yaml = file_put_contents($config_file, $yaml);
        header('Location: install.php?step=3');

      } catch(Exception $exception) {
          header('Location: ?step=2&error=invalid-api-key');
          exit;
      }

    } else {
      header('Location: ?step=2&error=no-api-key');
      exit;
    }

  } else {

    if( !isset($config['api']['key']) or empty($config['api']['key']) ) {
      header('Location: ?step=2&error=no-api-key');
      exit;
    }

    ?>

    <?php get_header(); ?>

    <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?step=4" method="POST" class="pure-form pure-form-aligned">
      <fieldset>
        <legend><?php _e('Guild'); ?></legend>
        <div class="pure-control-group">
          <label for="guild_name"><?php _e('Name'); ?></label>
          <input type="text" name="config[guild][name]" id="guild_name" class="pure-input-1-2" required />
        </div>
        <div class="pure-controls">
          <button type="submit" class="pure-button pure-button-primary"><?php _e('Next step'); ?>&nbsp;&raquo;</button>
        </div>
      </fieldset>
    </form>

    <?php get_footer(); ?>

    <?php

  }

 ?>
