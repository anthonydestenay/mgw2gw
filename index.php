<?php use PhpGw2Api\Cache; require_once 'bootstrap.php'; ?>

<?php

$api = new \GW2Treasures\GW2Api\GW2Api();

$cache = new Cache;
$dir = $cache->setDirectory(__DIR__."/cache");

$markdown = new \cebe\markdown\Markdown();

Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem(__DIR__ . '/themes');
$twig = new Twig_Environment($loader, array(
    'cache' => __DIR__ . '/cache',
));

if(!$cache->hasCache('guild_log')) {
  $guild_log = $api->guild()->log($config['api']['key'], $config['guild']['id'])->get(1);
  $cache->save('guild_log', $guild_log);
} else {
  $guild_log = $cache->retrieve('guild_log');
}

if(!$cache->hasCache('guild_members')) {
  $guild_members = $api->guild()->members($config['api']['key'], $config['guild']['id'])->get();
  $cache->save('guild_members', $guild_members);
} else {
  $guild_members = $cache->retrieve('guild_members');
}

if(!$cache->hasCache('guild_ranks')) {
  $guild_ranks = $api->guild()->ranks($config['api']['key'], $config['guild']['id'])->get();
  $cache->save('guild_ranks', $guild_ranks);
} else {
  $guild_ranks = $cache->retrieve('guild_ranks');
}

$is_admin = false;

if( isset($_SESSION['admin']) && !empty($_SESSION['admin']) ) {
  if($_SESSION['admin'] === base64_encode(sha1($config['admin']['username'].$config['admin']['password']))) {
    $is_admin = true;
  }
}

if( isset($config['guild']['types']) && !empty($config['guild']['types']) ) {
  foreach($config['guild']['types'] as $type) {
    $types[$type] = $_guild_types[$type];
  }
} else {
  $types = null;
}

if( isset($config['guild']['activities']) && !empty($config['guild']['activities']) ) {
  foreach($config['guild']['activities'] as $activity) {
    $activities[$activity] = $_guild_activities[$activity];
  }
} else {
  $activities = null;
}

if( isset($guild_log) && !empty($guild_log) ) {
  $i = 0;
  foreach($guild_log as $log):

    if($i == 15) {
      continue;
    }

    if( isset($log->user) && !empty($log->user) ) {

      $user = explode('.', $log->user);
      $log->user = $user[0];

      if(isset($log->invited_by) && !empty($log->invited_by)) {
        $user = explode('.', $log->invited_by);
        $log->invited_by = $user[0];
      }

      if(isset($log->kicked_by) && !empty($log->kicked_by)) {
        $user = explode('.', $log->kicked_by);
        $log->kicked_by = $user[0];
      }

      if(isset($log->changed_by) && !empty($log->kicked_by)) {
        $user = explode('.', $log->changed_by);
        $log->changed_by = $user[0];
      }

      switch($log->type) {
        case 'joined':
          $content = sprintf( __('%s has joined the guild.'), $log->user);
        break;
        case 'invited':
          $content = sprintf( __('%s has invited %s to the guild.'), $log->invited_by, $log->user );
        break;
        case 'invite_declined':
          $content = sprintf( __('%s has declined the invite to join the guild.'), $log->user );
        break;
        case 'kick':
          $content = sprintf( __('%s has kicked %s from the guild.'), $log->kicked_by, $log->user );
        break;
        case 'rank_change':
          if(isset($log->changed_by) && !empty($log->changed_by)) {
            $content = sprintf( __('%s has changed the role of %s to %s.'), $log->changed_by, $log->user, $log->new_rank );
          } else {
            $content = sprintf( __('%s has promoted %s.'), $log->user, $log->new_rank );
          }

        break;
        case 'motd':
          $content = sprintf( __('%s has changed the Message of the Day:'), $log->user );
          $content .= "\n".$log->motd;
        break;
        case 'stash':
          if( $log->item_id && isset($log->operation) ) {
            if(!$cache->hasCache('item_'.$log->item_id)) {
                $item = $api->items()->lang($config['language'])->get($log->item_id);
                $cache->save('item_'.$log->item_id, $item);
            } else {
              $item = $cache->retrieve('item_'.$log->item_id);
            }

            if($log->operation == 'deposit') {
              $content = sprintf( __('%s deposited %s (×%s).'), $log->user, $item->name, $log->count );
            } else {
              $content = sprintf( __('%s has withdrawn %s (×%s).'), $log->user, $item->name, $log->count );
            }
          }
        break;
        case 'treasury':
          if(!$cache->hasCache('item_'.$log->item_id)) {
              $item = $api->items()->lang($config['language'])->get($log->item_id);
              $cache->save('item_'.$log->item_id, $item);
          } else {
            $item = $cache->retrieve('item_'.$log->item_id);
          }
          $content = sprintf( __('%s deposited %s (×%s).'), $log->user, __($log->operation), $item->name, $log->count );
        break;
        case 'upgrade':
          if(!$cache->hasCache('upgrade_'.$log->upgrade_id)) {
              $upgrade = $api->guild()->upgrades()->lang($config['language'])->get($log->upgrade_id);
              $cache->save('upgrade_'.$log->upgrade_id, $upgrade);
          } else {
            $upgrade = $cache->retrieve('upgrade_'.$log->upgrade_id);
          }
          $content = sprintf( __('%s queued %s.'), $log->user, $upgrade->name );
        break;
      }

      if($content) {
        $logs[] = array(
          'id' => $log->id,
          'type' => $log->type,
          'content' => $content,
          'date' => $log->time
        );
        $i++;
      }

    }

  endforeach;
} else {
  $logs = null;
}

foreach($guild_ranks as $rank) {
  $guild_rank_icon[$rank->id] = $rank->icon;
}

if(isset($guild_members) && !empty($guild_members)) {
  foreach($guild_members as $member) {

    $user = explode('.', $member->name);
    $member->name = $user[0];

    $members[] = array(
      'name' => $member->name,
      'joined' => $member->joined,
      'rank' => array(
        'name' => $member->rank,
        'icon' => $guild_rank_icon[$member->rank]
      )
    );
  }
} else {
  $members = null;
}

if(isset($config['guild']['charter']) && !empty($config['guild']['charter'])) {
  $charter = $markdown->parse($config['guild']['charter']);
} else {
  $charter = null;
}

if(isset($config['recruitment']['infos']) && !empty($config['recruitment']['infos'])) {
  $recruitment_infos = $markdown->parse($config['recruitment']['infos']);
} else {
  $recruitment_infos = null;
}

foreach($_recruitement_professions as $k => $v) {
  $recruitment_professions[$k] = array(
    'profession' => $v,
    'status' => array(
      'id' => ( !isset($config['recruitment']['professions'][$k]['status']) or empty($config['recruitment']['professions'][$k]['status']) or $config['recruitment']['professions'][$k]['status'] == 'closed' ) ? 'closed' : 'open',
      'name' => ( !isset($config['recruitment']['professions'][$k]['status']) or empty($config['recruitment']['professions'][$k]['status']) or $config['recruitment']['professions'][$k]['status'] == 'closed' ) ? __('Closed') : __('Open')
    ),
    'level' => ( !isset($config['recruitment']['professions'][$k]['level']) or empty($config['recruitment']['professions'][$k]['level']) ) ? 1 : $config['recruitment']['professions'][$k]['level'],
  );
}

if( isset($config['links']) && !empty($config['links']) ) {
  foreach($_links as $k => $v) {
    if(isset($config['links'][$k]) && !empty($config['links'][$k])) {
      $links[$k] = array(
        'name' => $v,
        'url' => $config['links'][$k]
      );
    }
  }
} else {
  $links = null;
}

$feed_items = null;
if( isset($config['feed']['url']) && !empty($config['feed']['url']) ) {
  $feed_hash = sha1($config['feed']['url']);
  if(!$cache->hasCache('feed_items_'.$feed_hash)) {
    $feed_url = $config['feed']['url'];

    $xml = @simplexml_load_file($feed_url);
    $feed_items = array();

    if($xml) {

      if(isset($xml->channel->item) && !empty($xml->channel->item)) {
        foreach($xml->channel->item as $item) {
          $feed_items[] = array(
            'title' => (string) trim($item->title),
            'link' => (string) $item->link,
            'category' => (string) $item->category,
            'date' => (string) strtotime($item->pubDate)
          );
        }
      } elseif(isset($xml->entry) && !empty($xml->entry)) {
        foreach($xml->entry as $item) {
          $feed_items[] = array(
            'title' => (string) trim($item->title),
            'link' => (string) $item->link->attributes()['href'],
            'category' => (string) $item->category->attributes()['term'],
            'date' => (string) strtotime($item->updated)
          );
        }
      }
      $cache->save('feed_items-'.$feed_hash, $feed_items);
    }
  } else {
    $feed_items = $cache->retrieve('feed_items_'.$feed_hash);
  }
}

$data = array(
  'theme' => ( isset($config['theme']) && !empty($config['theme']) ) ? '/themes/'.$config['theme'] : '/themes/default',
  'is_admin' => $is_admin,
  'guild' => array(
    'name' => $config['guild']['name'],
    'tag' => $config['guild']['tag'],
    'emblem' => array(
      'background' => $config['guild']['emblem']['background'],
      'foreground' => $config['guild']['emblem']['foreground']
    ),
    'types' => $types,
    'activities' => $activities,
    'log' => $logs,
    'members' => $members,
    'charter' => $charter
  ),
  'recruitment' => array(
    'show' => ( isset($config['recruitment']['show']) && !empty($config['recruitment']['show']) && $config['recruitment']['show'] == 1 ) ? true : false,
    'infos' => $recruitment_infos,
    'professions' => $recruitment_professions
  ),
  'links' => $links,
  'feed' => array(
    'items' => $feed_items,
    'limit' => ( isset($config['feed']['limit']) && !empty($config['feed']['limit']) ) ? $config['feed']['limit'] : 10,
  ),
  'title' => array(
    'activity' => __('Activity'),
    'members' => __('Members'),
    'charter' => __('Charter'),
    'recruitment' => __('Recruitment'),
    'links' => __('Find us on...'),
    'newsfeed' => __('Newsfeed')
  ),
  'credits' => array(
    'made_by' => sprintf( __('Made with %s by %s'), '<i class="fa fa-heart"></i>', '<a href="http://you.an-d.me" target="_blank">Anthony Destenay</a>'),
    'copyrights' => __('All associated logos and designs are trademarks or registered trademarks of NCSOFT Corporation.')
  ),
  'google_analytics' => ( isset($config['google_analytics']) && !empty($config['google_analytics']) ) ? $config['google_analytics'] : null
);

if(isset($config['theme']) && !empty($config['theme'])) {
  $theme = $config['theme'];
  if(file_exists(__DIR__ . "/themes/$theme/index.html.twig")) {
    echo $twig->render("$theme/index.html.twig", $data);
  } else {
    die('No "index.html.twig" file in theme "'.$theme.'".');
  }
} else {
  if(file_exists(__DIR__ . "/themes/default/index.html.twig")) {
    echo $twig->render("default/index.html.twig", $data);
  } else {
    die('No choosen theme in admin or default theme.');
  }
}

?>
