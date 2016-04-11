<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php _e('Installation'); ?></title>

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
          <li><a href="http://you.an-d.me/contact" target="_blank"><?php _e('Need help?'); ?></a></li>
        </ul>

        <h1>My<span>&nbsp;</span>GuildWebsite <small><?php _e('Installation'); ?> | <?php echo sprintf( __('Step %s'), $step ); ?></small></h1>
