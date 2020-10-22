<?php
    if (!defined('FREEPBX_IS_AUTH')) { exit('No direct script access allowed'); }

    $list_menu = array(
        'setting' => array(
            'label' => _('Settings'),
            'icon' => 'fa-cog',
            'action' => 'settings',
        ),
        'msgs' => array(
            'label' => _('Messages'),
            'icon' => 'fa-globe',
            'action' => 'messages',
        ),
    );
?>

<br>
<div class="list-group">
<?php
    $template = '<a href="?display=%s&amp;action=%s" class="list-group-item %s"><i class="fa %s">&nbsp;&nbsp;</i>%s</a>';
    foreach ($list_menu as $k => $v)
    {
        $active = $v['action'] == $request['action'] ? 'active' : '';
        echo sprintf($template, $request['display'], $v['action'], $active , $v['icon'], $v['label']);
        // echo '<span class="list-group-item">';
        // echo '<a href="?display=hotelwakeup_settings&amp;action='. $v['action'] .'" class="btn btn-block list-group-item"><i class="fa ' . $v['icon'] . '"></i>&nbsp; ' . $v['label'] . '</a>';
		// echo '</span>';
	}
?>    
</div>