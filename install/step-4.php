<?php

  if($_POST) {

    if( isset($_POST['config']['guild']['name']) && !empty($_POST['config']['guild']['name']) ) {
      $guild_name = htmlentities(trim($_POST['config']['guild']['name']));
    } else {
      header('Location: ?step=3');
      exit;
    }

    $service = new PhpGw2Api\Service(__DIR__ . '/cache', 3600);
    $service = $service->returnAssoc(true);
    $guild = $service->getGuildDetails(array('guild_name' => $guild_name));

    $foregrounds = @file_get_contents('https://api.guildwars2.com/v2/emblem/foregrounds?ids='.$guild['emblem']['foreground_id']);
    $foregrounds = json_decode($foregrounds);
    $foreground = $foregrounds[0]->layers[0];
    $backgrounds = @file_get_contents('https://api.guildwars2.com/v2/emblem/backgrounds?ids='.$guild['emblem']['background_id']);
    $backgrounds = json_decode($backgrounds);
    $background = $backgrounds[0]->layers[0];

    $colors = $service->getColors(array('lang' => 'fr'));

    ?>

    <?php get_header(); ?>

    <div class="pure-g">
      <div class="pure-u-1-5">
        <div class="guild-emblem" style="background-image: url('<?php echo $background; ?>');">
          <img src="<?php echo $foreground; ?>" />
        </div>
      </div>
      <div class="pure-u-4-5">
        <div class="guild-name"><?php echo $guild['guild_name']; ?></div>
      </div>
    </div>

    <div class="pure-g mtxl">
      <div class="pure-u-1-2">
        <p class="m0"><a href="?step=3" class="pure-button pure-button-warning">&laquo;&nbsp;<?php _e('Go back! It\'s not my guild...'); ?></a></p>
      </div>
      <div class="pure-u-1-2 tar">
        <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?step=5" method="POST">
          <input type="hidden" name="config[guild][id]" value="<?php echo $guild['guild_id']; ?>" />
          <input type="hidden" name="config[guild][name]" value="<?php echo $guild['guild_name']; ?>" />
          <input type="hidden" name="config[guild][tag]" value="<?php echo $guild['tag']; ?>" />
          <input type="hidden" name="config[guild][emblem][foreground]" value="<?php echo $foreground; ?>" />
          <input type="hidden" name="config[guild][emblem][background]" value="<?php echo $background; ?>" />
          <button type="submit" class="pure-button pure-button-primary"><?php _e('This is my guild! Next step'); ?>&nbsp;&raquo;</button>
        </form>
      </div>
    </div>

    <?php get_footer(); ?>

    <?php

  } else {
    header('Location: ?step=3&error=no-guild-name');
    exit;
  }

 ?>
