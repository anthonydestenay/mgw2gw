<?php require_once 'bootstrap.php'; ?>

<?php

  if( isset($_SESSION['admin']) && !empty($_SESSION['admin']) && !isset($_GET['login']) ) {
    if($_SESSION['admin'] !== base64_encode(sha1($config['admin']['username'].$config['admin']['password']))) {
      header('Location: ?login&error=bad-session');
      exit;
    }
  } elseif( !isset($_GET['login']) ) {
    header('Location: ?login');
    exit;
  }

  if(isset($_GET['login'])) {
    if($_POST) {

      if(

        isset($_POST['username']) && !empty($_POST['username']) &&
        isset($_POST['password']) && !empty($_POST['password'])

      ) {

        $username = $_POST['username'];
        $password = sha1($_POST['password']);

        if( $username === $config['admin']['username'] && $password === $config['admin']['password'] ) {
          $_SESSION['admin'] = base64_encode(sha1($username.$password));
          header('Location: admin.php');
          exit;
        } else {
          header('Location: ?login&error=wrong-username-or-password');
          exit;
        }

      }

    }
  } elseif(isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
  } else {

    if($_POST) {

      $_POST['config']['guild']['id'] = $config['guild']['id'];
      $_POST['config']['guild']['tag'] = $config['guild']['tag'];
      $_POST['config']['guild']['name'] = $config['guild']['name'];
      $_POST['config']['api']['key'] = $config['api']['key'];

      if(isset($_POST['config']['admin']['password']) && !empty($_POST['config']['admin']['password'])) {
        $password = $_POST['config']['admin']['password'];
        $_POST['config']['admin']['password'] = sha1($password);
      } else {
        $_POST['config']['admin']['password'] = $config['admin']['password'];
      }

      foreach($_guild_types as $k => $v) {
        if(empty($_POST['config']['guild']['types'][$k])) { unset($_POST['config']['guild']['types'][$k]); }
      }

      foreach($_guild_activities as $k => $v) {
        if(empty($_POST['config']['guild']['activities'][$k])) { unset($_POST['config']['guild']['activities'][$k]); }
      }

      foreach($_links as $k => $v) {
        if(empty($_POST['config']['links'][$k])) { unset($_POST['config']['links'][$k]); }
      }

      $yaml = Spyc::YAMLDump($_POST['config']);
      $yaml = file_put_contents('config.yaml', $yaml);

      deletecache('cache');

      header('Location: ?update=ok');

    }

  }

 ?>

 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width, initial-scale=1">

     <title><?php _e('Administration'); ?> | MGW</title>

     <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
     <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-min.css">
     <link rel="stylesheet" href="themes/admin.styles.css">
     <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

     <!--[if lt IE 9]>
       <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
       <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
     <![endif]-->
   </head>
   <body>

     <div class="pure-g container">

       <div class="pure-u-1">

         <ul id="menu">
           <li><a href="index.php"><?php _e('Visit site'); ?></a></li>
           <?php if(!isset($_GET['login'])): ?><li><a href="?logout"><?php _e('Logout'); ?></a></li><?php endif; ?>
         </ul>

         <h1>My<span>&nbsp;</span>GuildWebsite <small><?php _e('Administration'); ?> | <?php _e('Site settings'); ?></small></h1>

    <?php

    if(isset($_GET['login'])) {

      ?>

      <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?login" method="POST" class="pure-form pure-form-aligned">
        <fieldset>
          <legend><?php _e('Login'); ?></legend>
          <div class="pure-control-group">
            <label for="username"><?php _e('Username'); ?></label>
            <input type="text" name="username" id="username" class="pure-input-1-2" required />
          </div>
          <div class="pure-control-group">
            <label for="password"><?php _e('Password'); ?></label>
            <input type="password" name="password" id="password" class="pure-input-1-2" required />
          </div>
          <div class="pure-controls">
            <button type="submit" class="pure-button pure-button-primary"><?php _e('Login'); ?>&nbsp;&raquo;</button>
          </div>
        </fieldset>
      </form>

      <?php

    } else {

    ?>

    <?php

      if( isset($_GET['update']) ) {
        switch($_GET['update']) {
          case 'ok' :
            echo '<div class="alert alert-success">'. __('The configuration\'s settings has been saved.') .'</div>';
            break;

          default:
            header('Location: admin.php');
            break;
        }
      }

     ?>

    <form method="POST" action="<?php echo htmlentities($_SERVER["PHP_SELF"]); ?>" class="pure-form pure-form-aligned">
      <fieldset>
        <legend><?php _e('Login credentials'); ?></legend>

        <div class="pure-control-group">
          <label for="username"><?php _e('Username'); ?></label>
          <input type="text" id="username" class="pure-input-1-2" name="config[admin][username]" value="<?php echo $config['admin']['username']; ?>" />
        </div>

        <div class="pure-control-group">
          <label for="password"><?php _e('Password'); ?></label>
          <input type="password" id="password" class="pure-input-1-2" name="config[admin][password]" value="" />
          <p class="help-text"><i class="fa fa-info-circle"></i> <?php _e('Leave "Password" empty for not change it.'); ?></p>
        </div>
      </fieldset>

      <fieldset>
        <legend><?php _e('Language'); ?></legend>
        <div class="pure-control-group">
          <label for="language"><?php _e('Language'); ?></label>
          <select name="config[language]" id="language">
          <?php foreach($_languages as $k => $v): ?>
            <option value="<?php echo $k; ?>" <?php if($config['language'] == $k): ?>selected<?php endif; ?>><?php echo $v; ?></option>
          <?php endforeach; ?>
          </select>
        </div>
      </fieldset>

      <?php

        $themes_dir = __DIR__ . '/themes';

        $td = scandir($themes_dir);
        array_shift($td);
        array_shift($td);

        $i = 0;
        foreach($td as $t) {
          $theme_dir = __DIR__ . "/themes/$t";
          if(is_dir( $theme_dir )) {
            $themes[$i]['id'] = $t;

            $theme_screenshot = "$theme_dir/screenshot.jpg";
            if(file_exists( $theme_screenshot )) {
              $themes[$i]['screenshot'] = $theme_screenshot;
            } else {
              $themes[$i]['screenshot'] = false;
            }

            $i++;
          }
        }

      ?>

      <fieldset>
        <legend><?php _e('Theme'); ?></legend>
        <?php if( isset($themes) && !empty($themes) ): ?>
          <div class="pure-g" id="themes">
          <?php foreach($themes as $th): ?>
            <div class="pure-u-1-3">
              <label for="themes_<?php echo $th['id']; ?>" class="pure-radio">
                  <input type="radio" id="themes_<?php echo $th['id']; ?>" value="<?php echo $th['id']; ?>" name="config[theme]" required <?php if( isset($config['theme']) && !empty($config['theme']) && $config['theme'] == $th['id']): ?>checked<?php endif; ?> />
                  <img src="http://placehold.it/1024x768?text=<?php echo $th['id']; ?>" class="pure-img" />
              </label>
            </div>
          <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </fieldset>

      <fieldset>
        <legend><?php _e('Guild'); ?></legend>
        <div class="pure-control-group">
          <label for="guild_id"><?php _e('ID'); ?></label>
          <input type="text" id="guild_id" class="pure-input-1-2" name="config[guild][id]" value="<?php echo $config['guild']['id']; ?>" required disabled />
        </div>
        <div class="pure-control-group">
          <label for="guild_charter"><?php _e('Charter'); ?></label>
          <textarea id="guild_charter" name="config[guild][charter]" class="pure-input-2-3" rows="10"><?php if( isset($config['guild']['charter']) && !empty($config['guild']['charter']) ) { echo $config['guild']['charter']; } ?></textarea>
          <p class="help-text"><i class="fa fa-info-circle"></i> <?php _e('U can use Markdown in this field.'); ?> <a href="https://guides.github.com/features/mastering-markdown/" target="_blank"><?php _e('Learn more'); ?>&nbsp;&raquo;</a></p>
        </div>
      </fieldset>
      <fieldset>
        <legend><?php _e('Type'); ?></legend>
        <div class="pure-g">
        <?php foreach($_guild_types as $k => $v): ?>
          <div class="pure-u-1 pure-u-sm-1-2 pure-u-md-1-3">
            <label for="guild_types_<?php echo $k; ?>" class="pure-checkbox">
                <input type="checkbox" id="guild_types_<?php echo $k; ?>" value="<?php echo $k; ?>" name="config[guild][types][]" <?php if( isset($config['guild']['types']) && !empty($config['guild']['types']) && in_array($k, $config['guild']['types'])): ?>checked<?php endif; ?> />
                <?php echo $v; ?>
            </label>
          </div>
        <?php endforeach; ?>
        </div>
      </fieldset>
      <fieldset>
        <legend><?php _e('Activities'); ?></legend>
        <div class="pure-g">
        <?php foreach($_guild_activities as $k => $v): ?>
          <div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
            <label for="guild_activities_<?php echo $k; ?>" class="pure-checkbox">
                <input type="checkbox" id="guild_activities_<?php echo $k; ?>" value="<?php echo $k; ?>" name="config[guild][activities][]" <?php if( isset($config['guild']['activities']) && !empty($config['guild']['activities']) && in_array($k, $config['guild']['activities'] ) ): ?>checked<?php endif; ?> />
                <?php echo $v; ?>
            </label>
          </div>
        <?php endforeach; ?>
        </div>
      </fieldset>

      <fieldset>
        <legend><?php _e('Recruitment'); ?></legend>
        <label for="recruitment_show" class="pure-checkbox mbxl">
            <input id="recruitment_show" type="checkbox" name="config[recruitment][show]" value="1" <?php if( isset($config['recruitment']['show']) && !empty($config['recruitment']['show']) && $config['recruitment']['show'] == 1 ): ?>checked<?php endif; ?>> <?php _e('Show recruitment section'); ?>
        </label>
        <p class="help-text help-block"><i class="fa fa-info-circle"></i> <?php _e('For each profession, select the recruitment status and the minimum level required.'); ?></p>

        <div class="pure-g" style="margin-bottom: 2em;">

          <?php foreach($_recruitement_professions as $k => $v): ?>

            <div class="pure-u-1 pure-u-sm-1-2 pure-u-md-1-4 recruitment-control recruitment-<?php echo $k; ?>">
              <h4><?php echo $v; ?></h4>
              <select name="config[recruitment][professions][<?php echo $k; ?>][status]">
                <option value="closed" <?php if( isset($config['recruitment']['professions'][$k]['status']) && !empty($config['recruitment']['professions'][$k]['status']) && $config['recruitment']['professions'][$k]['status'] == 'closed'): ?>selected<?php endif; ?>><?php _e('Closed'); ?></option>
                <option value="open" <?php if( isset($config['recruitment']['professions'][$k]['status']) && !empty($config['recruitment']['professions'][$k]['status']) && $config['recruitment']['professions'][$k]['status'] == 'open'): ?>selected<?php endif; ?>><?php _e('Open'); ?></option>
              </select>
              <select name="config[recruitment][professions][<?php echo $k; ?>][level]">
              <?php foreach($_recruitment_levels as $rl): ?>
                <option value="<?php echo $rl; ?>" <?php if(isset($config['recruitment']['professions'][$k]['level']) && !empty($config['recruitment']['professions'][$k]['level']) && $config['recruitment']['professions'][$k]['level'] == $rl): ?>selected<?php endif; ?>><?php echo $rl; ?></option>
              <?php endforeach; ?>
              </select>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="pure-control-group">
          <label for="recruitment_infos"><?php _e('Further information'); ?></label>
          <textarea id="recruitment_infos" name="config[recruitment][infos]" class="pure-input-2-3" rows="10"><?php if( isset($config['recruitment']['infos']) && !empty($config['recruitment']['infos']) ) { echo $config['recruitment']['infos']; } ?></textarea>
          <p class="help-text"><i class="fa fa-info-circle"></i> <?php _e('U can use Markdown in this field.'); ?> <a href="https://guides.github.com/features/mastering-markdown/" target="_blank"><?php _e('Learn more'); ?>&nbsp;&raquo;</a></p>
        </div>

      </fieldset>

      <fieldset>
        <legend><?php _e('RSS feed'); ?></legend>
        <div class="pure-control-group">
          <label for="feed_url"><?php _e('Feed URL'); ?></label>
          <input type="url" id="feed_url" class="pure-input-1-2" name="config[feed][url]" value="<?php if(isset($config['feed']['url'])) : echo $config['feed']['url']; endif; ?>" />
        </div>
        <div class="pure-control-group">
          <label for="feed_limit"><?php _e('Limit to'); ?></label>
          <select name="config[feed][limit]" id="feed_limit">
          <?php foreach($_feed_limits as $fl): ?>
            <option value="<?php echo $fl; ?>" <?php if(isset($config['feed']['limit']) && !empty($config['feed']['limit']) && $config['feed']['limit'] == $fl): ?>selected<?php endif; ?>><?php echo $fl; ?></option>
          <?php endforeach; ?>
          </select> &nbsp;<?php _e('the items\' number to display.'); ?>
        </div>
      </fieldset>

      <fieldset>
        <legend><?php _e('Links'); ?></legend>
        <?php foreach($_links as $k => $v): ?>
        <div class="pure-control-group">
          <label for="links_<?php echo $k; ?>"><?php echo $v; ?></label>
          <input type="url" id="links_<?php echo $k; ?>" class="pure-input-1-2" name="config[links][<?php echo $k; ?>]" value="<?php if(isset($config['links'][$k])) : echo $config['links'][$k]; endif; ?>" />
        </div>
      <?php endforeach; ?>
      </fieldset>

      <fieldset>
        <legend><?php _e('API'); ?></legend>
        <div class="pure-control-group">
          <label for="api_key"><?php _e('Key'); ?></label>
          <input type="text" id="api_key" class="pure-input-1-2" name="config[api][key]" value="<?php echo $config['api']['key']; ?>" required disabled />
        </div>
      </fieldset>

      <fieldset>
        <legend><?php _e('Google Analytics'); ?></legend>
        <div class="pure-control-group">
          <label for="google_analytics"><?php _e('Tracking ID'); ?></label>
          <input type="text" id="google_analytics" class="pure-input-1-2" name="config[google_analytics]" value="<?php if(isset($config['google_analytics']) && !empty($config['google_analytics'])): echo $config['google_analytics']; endif; ?>" />
        </div>
      </fieldset>

      <fieldset>
        <div class="pure-controls">
          <input type="hidden" name="config[guild][emblem][foreground]" value="<?php echo $config['guild']['emblem']['foreground']; ?>" />
          <input type="hidden" name="config[guild][emblem][background]" value="<?php echo $config['guild']['emblem']['background']; ?>" />
          <button type="submit" class="pure-button pure-button-primary"><?php _e('Save'); ?></button>
        </div>
      </fieldset>
    </form>

    <?php     } // not isset ?login ?>

      </div>
    </div>

    <div id="footer"<?php if( isset($_GET['login']) ): ?> class="inverse"<?php endif; ?>>
      <div class="container"><?php echo sprintf( __('Made with %s by %s'), '<i class="fa fa-heart"></i>', '<a href="http://you.an-d.me" target="_blank">Anthony Destenay</a>'); ?><br /><small><?php _e('All associated logos and designs are trademarks or registered trademarks of NCSOFT Corporation.'); ?></small></div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  </body>
</html>
