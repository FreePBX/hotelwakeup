<?php
    $all_msg  = $hotelwakeup->getMessageDefault();
    $info_grp = [
        'global' => [
            'id'    => 'grpGlobal',
            'label' => _("Gloabl"),
        ],
        'operator' => [
            'id'    => 'grpOperator',
            'label' => _("Operator"),
        ],
        'wakeup' => [
            'id'    => 'grpWakeUp',
            'label' => _("Wake Up Call Creation"),
        ],
        'confirm' => [
            'id'    => 'grpConfirm',
            'label' => _("Confirmation Call"),
        ],
    ];

    $info_input = [
        'SayUnixTime' => [
            'label'=> '',
            'help' => '<a href="https://www.voip-info.org/asterisk-cmd-sayunixtime/" target="_blank">https://www.voip-info.org/asterisk-cmd-sayunixtime/</a>',
            'play' => false,
        ],
		'welcome' => [
            'label'=> '',
            'help' => '',
            'play' => true,
        ],
		'goodbye' => [
            'label'=> '',
            'help' => '',
            'play' => true,
        ],
		'error' => [
            'label'=> '',
            'help' => '',
            'play' => true,
        ],
		'retry' => [
            'label'=> '',
            'help' => '',
            'play' => true,
        ],
		'optionInvalid' => [
            'label'=> '',
            'help' => '',
            'play' => true,
        ],
		'invalidDialing' => [
            'label'=> '',
            'help' => '',
            'play' => true,
        ],
		'operatorSelectExt' => [
            'label'=> '',
            'help' => '',
            'play' => true,
        ],
		'operatorEntered' => [
            'label'=> '',
            'help' => '',
            'play' => true,
        ],
		'wakeupMenu' => [
            'label'=> '',
            'help' => '',
            'play' => true,
        ],
		'wakeupAdd' => [
            'label'=> '',
            'help' => '',
            'play' => true,
        ],
		'wakeupAddType12H' => [
            'label'=> '',
            'help' => '',
            'play' => true,
        ],
		'wakeupAddOk' => [
            'label'=> '',
            'help' => '',
            'play' => true,
        ],
		'wakeupList' => [
            'label'=> '',
            'help' => '',
            'play' => true,
        ],
		'wakeupListEmpty' => [
            'label'=> '',
            'help' => '',
            'play' => true,
        ],
		'wakeupListInfoCall' => [
            'label'=> '',
            'help' => '',
            'play' => true,
        ],
		'wakeupListMenu' => [
            'label'=> '',
            'help' => '',
            'play' => true,
        ],
		'wakeupListCancelCall' => [
            'label'=> '',
            'help' => '',
            'play' => true,
        ],
		'wakeConfirmMenu' => [
            'label'=> '',
            'help' => '',
            'play' => true,
        ],
		'wakeConfirmDelay' => [
            'label'=> '',
            'help' => '',
            'play' => true,
        ],
    ];
?>


<h2><i class="fa fa-language">&nbsp;</i> Edit Message - <?php echo $language . "(".$lang.")" ?></h2>

<div class="panel panel-default" id="boxWildcards">
	<div class="panel-heading collapsed" data-target="#moreinfo" data-toggle="collapse" class="collapsed" aria-expanded="false">
        <h3 class="panel-title">
            <i class="fa fa-info fa-fw fa-lg"></i>&nbsp;&nbsp;&nbsp;<?php echo _('Wildcards') ?>
            <span class="pull-right">
                <i class="chevron fa fa-fw fa-lg"></i>
            </span>
        </h3>
    </div>
    <div class="panel-collapse collapse" id="moreinfo" aria-expanded="false" style="height: 0px;">
        <div class="panel-body ">
            <p><?php  echo _('To add silence between files we will use <b>"silence|xxx"</b>, <b>xxx</b> corresponding to the number of milliseconds we want the silence to last.') ?> </p>
            <p><?php  echo _('By default when detecting a number "say_number" is used, but if we want to use "say_digits" we will have to use the following format <b>"d|xxxx"</b>, <b>xxx</b> corresponds to the number.') ?> </p>
            <p><?php  echo _('SayUnixTime|xxxx') ?> </p>
        </div>
    </div>
</div>

<form id="messageform">
<div class="display full-border">
    <input id="language" name="language" type="hidden" value="<?php echo $lang ?>">
    <?php
    foreach($hotelwakeup->getGroupsMessages() as $k => $v)
    {
        if (! array_key_exists($k, $info_grp)) { continue; }
        $info = $info_grp[$k];
        echo sprintf('<div class="section-title" data-for="%s"><h3><i class="fa fa-minus fa-fw"></i>&nbsp;%s</h3></div>', $info['id'], $info['label']);
        echo sprintf('<div class="section" data-id="%s">', $info['id']);
        foreach($v as $sk)
        {
            $data = array(
                "hotelwakeup" => $hotelwakeup,
                'key'         => $sk,
                'value'       => implode(",", $all_msg[$sk]),
                'label'       => empty($info_input[$sk]['label']) ? ucfirst($sk) : $info_input[$sk]['label'],
                'help'        => $info_input[$sk]['help'],
                'default'     => implode(",", $hotelwakeup->getMessageDefault($sk, true)),
                'jplayer'     => $info_input[$sk]['play'],
            );
            if ($data['value'] == $data['default'])
            {
                $data['value'] = '';
            }
            echo $hotelwakeup->showPage('settings.message.edit.line', $data);
        }
        echo '</div>';
    }
    ?>
</div>
</form>

<?php
/*
    https://www.asterisksounds.org/es-es/node/25635
    it-now = ahora es
    prompt-not-found => No se encontró el archivo de audio
    cancelled = cancelado
    within = dentro de
    information = información
    in-the = en el
    in-the-line = En línea
    im-sorry = lo siento
    if-this-is-correct-press = Si esto es correcto, presione
    has-been = ha sido
    cancelled = cancelado
    your = su


    https://www.voip-info.org/asterisk-cmd-sayunixtime/
    SayUnixTime:
        en: IMpABd
        ja: BdApIM
        es: IMpAdB
    
    welcome
        en: hello&this-is-yr-wakeup-call
        ja: this-is-yr-wakeup-call
    
    optionInvalid:
        en: option-is-invalid

    error:
        en: an-error-has-occurred
    
    invalidDialing:
        en: you-entered&bad&digits

    retry:
        en: please-try-again
    
    operatorSelectExt:
        en: please-enter-the&number&for&your&wakeup-call&then-press-pound
    
    operatorEntered:
        en: you-entered&{num}

    goodbye:
        en: goodbye
    
    wakeupMenu:
        en: for-wakeup-call&press-1&list&press-2
        ja: en: for-wakeup-call&press-1&list&press-2&pleasepress2

    wakeupAdd:
        en: please-enter-the&time&for&your&wakeup-call
        ja: wakeup-call&jp-no&time&please-enter-the

    wakeupAddType12H:
        en: 1-for-am-2-for-pm
    
    wakeupAddOk:
        en: wakeup-call&added&digits/at&SayUnixTime{time}

    wakeupList:
        en: vm-youhave&{num call}&wakeup-call
        ja (num call > 0): wakeup-call&jp-wa&{num call}&jp-arimasu				
        ja (num call = 0): wakeup-call&jp-wa&jp-arimasen

    wakeupListInfoCall:
        en: wakeup-call&number&{number}&digits/at&SayUnixTime{time}
        ja: {number}&jp-banme&jp-no&wakeup-call&jp-wa&SayUnixTime{time}
        
    wakeupListMenu:
        en: to-cancel-wakeup&press-1&list&press-2&menu&press-3

    wakeupListCancelCall:
        en: wakeup-call-cancelled

    wakeConfirmMenu:
        en: to-snooze-for&5&minutes&press-1&to-snooze-for&10&minutes&press-2&to-snooze-for&15&minutes&press-3"
        ja: wakeup-menu

    wakeConfirmDelay:
        en: rqsted-wakeup-for&{num minutes}&minutes&vm-from&now
        ja: {num minutes}-minutes-from-now&rqsted-wakeup-for
*/