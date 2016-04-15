<?php

  if( $_POST ) {

    if(

      isset($_POST['config']['admin']['username']) && !empty($_POST['config']['admin']['username']) &&
      isset($_POST['config']['admin']['password']) && !empty($_POST['config']['admin']['password']) &&
      isset($_POST['config']['admin']['password_repeat']) && !empty($_POST['config']['admin']['password_repeat'])

    ) {

      $username = $_POST['config']['admin']['username'];
      $password = $_POST['config']['admin']['password'];
      $password_repeat = $_POST['config']['admin']['password_repeat'];

      if( !ctype_alnum($username) ) {
        header('Location: ?step=5&error=username-not-alnum');
        exit();
      }

      if( $password !== $password_repeat ) {
        header('Location: ?step=5&error=passwords-dont-match');
        exit();
      }

      try {

        unset($_POST['config']['admin']['password_repeat']);
        $_POST['config']['admin']['password'] = sha1($password);

        $config_file = __DIR__.'/../config.yaml';
        $data = array_merge($config, $_POST['config']);
        $yaml = Spyc::YAMLDump($data);
        $yaml = file_put_contents($config_file, $yaml);
        header('Location: install.php?step=5');
        exit();

      } catch(Exception $e) {
        header('Location: ?step=5&error=yaml-error');
        exit();
      }

    }

  } else {

    ?>

    <?php get_header(); ?>
    <h2><?php _e('Setup is completed!'); ?></h2>
    <p><?php _e('Remember to delete the file "install.php", the folder "/install" and his files.'); ?></p>
    <p><?php echo sprintf( __('You can now %s or %s.'), '<a href="index.php">'.__('visit your new site').'</a>', '<a href="admin.php">'.__('configure in administration').'</a>'); ?></p>
    <?php get_footer(); ?>
    <?php

  }

?>
