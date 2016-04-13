<?php session_start();

require_once 'vendor/autoload.php';

if(!file_exists('config')) {
  mkdir('config');
}

if(!file_exists('config.yaml')) {

  $page = htmlentities($_SERVER['PHP_SELF']);
  $page = explode('/', $page);
  $page = end($page);

  if($page !== 'install.php') {
    header('Location: install.php');
  }
}

$config = Spyc::YAMLLoad('config.yaml');

$_guild_types = array(
  'casual' => __('Casual'),
  'hardcore' => __('Hardcore'),
  'roleplay' => __('Roleplay'),
  'leveling' => __('Leveling'),
  'friendly' => __('Friendly'),
  'locale' => __('Locale')
);

$_guild_activities = array(

  'dynamic_events' => __('Dynamic events'),
  'world_bosses' => __('World bosses'),
  'dungeons' => __('Dungeons'),
  'fractals' => __('Fractals of the Mists'),
  'raids' => __('Raids'),
  'guild_missions' => __('Guild missions'),
  'wvw' => __('World versus World (WvW)'),
  'pvp' => __('Player versus Player (PvP)'),
  'pve' => __('Player versus Environment (PvE)')
);

$_recruitement_professions = array(
  'engineer' => __('Engineer'),
  'necromancer' => __('Necromancer'),
  'thief' => __('Thief'),
  'elementalist' => __('Elementalist'),
  'warrior' => __('Warrior'),
  'ranger' => __('Ranger'),
  'mesmer' => __('Mesmer'),
  'guardian' => __('Guardian')
);

$_languages = array(
  'en' => 'English',
  'fr' => 'Français'
);

$_recruitment_levels = array(1, 10, 20, 30, 40, 50, 60, 70, 80);

$_links = array(
  'website' => __('Website'),
  'blog' => 'Blog',
  'email' => 'E-mail',
  'facebook' => 'Facebook',
  'twitter' => 'Twitter',
  'google_plus' => 'Google+',
  'reddit' => 'Reddit',
  'youtube' => 'YouTube',
  'twitch' => 'Twitch',
  'tumblr' => 'Tumblr',
  'wordpress' => 'WordPress',
  'medium' => 'Medium',
  'voice_chat' => __('Voice chat')
);

$_feed_limits = array(1, 5, 10, 15, 20, 25, 50, 100);

function __($string) {
  global $config;
  $lang = (isset($config['language']) && !empty($config['language'])) ? $config['language'] : 'en';
  $file = __DIR__."/languages/$lang.yaml";

  if(file_exists($file)) {
    $trad = Spyc::YAMLLoad($file);

    if(isset($trad[$string])) {
      $string = $trad[$string];
    }
  }

  return $string;
}

function _e($string) {
  global $config;
  $lang = (isset($config['language']) && !empty($config['language'])) ? $config['language'] : 'en';
  $file = __DIR__."/languages/$lang.yaml";

  if(file_exists($file)) {
    $trad = Spyc::YAMLLoad($file);

    if(isset($trad[$string])) {
      $string = $trad[$string];
    }
  }

  echo $string;
}

function deletecache($dir) {
   $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
      (is_dir("$dir/$file")) ? deletecache("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
  }
