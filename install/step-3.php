<?php

  $api = new \GW2Treasures\GW2Api\GW2Api();

  if($_POST) {

    if( isset($_POST['config']['api']['key']) && !empty($_POST['config']['api']['key']) ) {

      try {

        $account = $api->account(trim($_POST['config']['api']['key']))->get();

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

    $account = $api->account($config['api']['key'])->get();

    if($account->guilds) {

      $service = new PhpGw2Api\Service(__DIR__ . '/cache', 3600);
      $service = $service->returnAssoc(true);
      $guilds = array();

      $i = 0;
      foreach($account->guilds as $guild) {
        try {
          $log = $api->guild()->log($config['api']['key'], $guild)->get();
        } catch(Exception $e) {
          $log = null;
        }

        if($log) {
          $guilds[$i] = $service->getGuildDetails(array('guild_id' => $guild));
          $foreground = $api->emblem()->foregrounds()->get( $guilds[$i]['emblem']['foreground_id'] );
          $guilds[$i]['foreground_img'] = $foreground->layers[0];
          $background = $api->emblem()->backgrounds()->get( $guilds[$i]['emblem']['background_id'] );
          $guilds[$i]['background_img'] = $background->layers[0];
          $i++;
        }

      }
    }

    if(count($guilds) <= 0) {
      header('Location: ?step=2&error=no-guild-leadership');
      exit;
    }

    ?>

    <?php get_header(); ?>

    <?php foreach($guilds as $k => $guild): ?>
      <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?step=4" method="POST" class="pure-form pure-form-aligned">
        <?php if($k == 0): ?><legend><?php _e('Choose your guild'); ?></legend><?php endif; ?>
        <div class="pure-g">
          <div class="pure-u-3-5 guild-name"><img src="<?php echo $guild['foreground_img']; ?>" width="34" height="34" align="left" /> <span>[<?php echo $guild['tag']; ?>]</span> <strong><?php echo $guild['guild_name']; ?></strong></div>
          <div class="pure-u-2-5 tar"><button type="submit" class="pure-button pure-button-primary"><?php _e('Choose this one'); ?>&nbsp;&raquo;</button></div>
        </div>
        <input type="hidden" name="config[guild][id]" value="<?php echo $guild['guild_id']; ?>" />
        <input type="hidden" name="config[guild][name]" value="<?php echo $guild['guild_name']; ?>" />
        <input type="hidden" name="config[guild][tag]" value="<?php echo $guild['tag']; ?>" />
        <input type="hidden" name="config[guild][emblem][foreground]" value="<?php echo $guild['foreground_img']; ?>" />
        <input type="hidden" name="config[guild][emblem][background]" value="<?php echo $guild['background_img']; ?>" />
      </form>
    <?php endforeach; ?>

    <?php get_footer(); ?>

    <?php

  }

 ?>
