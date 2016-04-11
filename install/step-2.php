<?php

  if($_POST) {

    try {
      $_POST['config']['theme'] = 'default';
      $config_file = __DIR__.'/../config.yaml';
      $yaml = Spyc::YAMLDump($_POST['config']);
      $yaml = file_put_contents(__DIR__.'/../config.yaml', $yaml);
      header('Location: install.php?step=2');
      exit();
    } catch (Exception $e) {
      header('Location: ?step=1&error=yaml-error');
      exit();
    }

  } else {

    if( !isset($config['language']) or empty($config['language']) ) {
      header('Location: ?step=1&error=yaml-error');
    }

    ?>

    <?php get_header(); ?>

    <p>Avant de continuer, vous devez vous assurer de deux choses :</p>

    <ol>
      <li>Vous devez être le chef de la guilde dont vous souhaitez créer le site, sinon demandez au chef de réaliser ce qui suit ;</li>
      <li>Vous devez avec un clé API, pour ce faire :
        <ol>
          <li>Rendez-vous sur <a href="https://account.arena.net/applications/create" target="_blank">account.arena.net/applications/create</a> ;</li>
          <li>Après vous être identifié, vous serez automatiquement redirigé vers la page de création de clé ;</li>
          <li>Saisissez un nom pour cette nouvelle clé ;</li>
          <li>Cochez l'élément "guilds" ;</li>
          <li>Cliquez sur le bouton "Créer une clé d'application" ;</li>
          <li>Sélectionnez puis copiez-collez cette clé dans le champ suivant.</li>
        </ol>
      </li>
    </ol>

    <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?step=3" method="POST" class="pure-form pure-form-aligned">
      <fieldset>
        <legend><?php _e('API'); ?></legend>
        <div class="pure-control-group">
          <label for="api_key"><?php _e('Key'); ?></label>
          <input type="text" class="pure-input-1-2" name="config[api][key]" id="api_key" required />
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
