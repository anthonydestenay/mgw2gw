<?php get_header(); ?>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>?step=2" method="POST" class="pure-form pure-form-aligned">
  <fieldset>
    <legend><?php _e('Choose language'); ?></legend>
    <div class="pure-control-group">
      <label for="language"><?php _e('Language'); ?></label>
      <select name="config[language]" id="language">
      <?php foreach($_languages as $k => $v): ?>
        <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
      <?php endforeach; ?>
      </select>
    </div>
    <div class="pure-controls">
      <button type="submit" class="pure-button pure-button-primary"><?php _e('Next step'); ?>&nbsp;&raquo;</button>
    </div>
  </fieldset>
</form>
<?php get_footer(); ?>
