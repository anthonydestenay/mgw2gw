<?php

  if($_POST) {

    if( isset($_POST['config']['guild']['id']) && !empty($_POST['config']['guild']['id']) ) {

      try {

        $api = new \GW2Treasures\GW2Api\GW2Api();
        $log = $api->guild()->log($config['api']['key'], $_POST['config']['guild']['id'])->get();

        $config_file = __DIR__.'/../config.yaml';

        $data = array_merge($config, $_POST['config']);
        $yaml = Spyc::YAMLDump($data);
        $yaml = file_put_contents($config_file, $yaml);
        header('Location: install.php?step=4');
        exit;

      } catch(Exception $e) {
        header('Location: install.php?step=3&error=not-the-leader');
        exit;
      }

    }

  } else {

    ?>

    <?php get_header(); ?>

    <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?step=5" method="POST" class="pure-form pure-form-aligned">
      <fieldset>
        <legend><?php _e('Login credentials'); ?></legend>
        <div class="pure-control-group">
          <p class="help-text help-block mbxl"><i class="fa fa-info-circle"></i> <?php _e('You use them to access the administration of your site.'); ?></p>
        </div>
        <div class="pure-control-group">
          <label for="admin_username"><?php _e('Username'); ?></label>
          <input type="text" name="config[admin][username]" id="admin_username" class="pure-input-1-2" required />
        </div>
        <div class="pure-control-group">
          <label for="admin_password"><?php _e('Password'); ?></label>
          <input type="password" name="config[admin][password]" id="admin_password" class="pure-input-1-2" required />
        </div>
        <div class="pure-control-group">
          <label for="admin_password_repeat"><?php _e('Password repeat'); ?></label>
          <input type="password" name="config[admin][password_repeat]" id="admin_password_repeat" class="pure-input-1-2" required />
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
