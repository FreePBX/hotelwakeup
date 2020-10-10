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
            'label'=> 'SayUnixTime Format',
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
            'help' => _('Message when an error occurs.'),
            'play' => true,
        ],
		'retry' => [
            'label'=> '',
            'help' => _('Message to inform that you retry the operation.'),
            'play' => true,
        ],
		'optionInvalid' => [
            'label'=> _('Invalid Option'),
            'help' => _('Message that informs that the selected operation is invalid.'),
            'play' => true,
        ],
		'invalidDialing' => [
            'label'=> _('Invalid Dialing'),
            'help' => _('Message when dialing invalid digits.'),
            'play' => true,
        ],
		'operatorSelectExt' => [
            'label'=> _('Select Extension'),
            'help' => _('Message asking the operator for the extension number.'),
            'play' => true,
        ],
		'operatorEntered' => [
            'label'=> _('Accessing Extension'),
            'help' => _('Message that informs the operator of the extension ({num}) in which you are accessing.'),
            'play' => true,
        ],
		'wakeupMenu' => [
            'label'=> _('Options Menu'),
            'help' => _('Wake up call service menu options message. Key 1 to create call and key 2 for list of created calls.'),
            'play' => true,
        ],
		'wakeupAdd' => [
            'label'=> _('Create'),
            'help' => _('Message requesting the data to create the wake-up call.'),
            'play' => true,
        ],
		'wakeupAddType12H' => [
            'label'=> 'AM/PM Time',
            'help' => _('Message that asks us if the time entered is AM (press key 1) or PM (press key 2).'),
            'play' => true,
        ],
		'wakeupAddOk' => [
            'label'=> _('Successfully Created'),
            'help' => _('Message informing that the wake-up call has been created successfully ({time}).'),
            'play' => true,
        ],
		'wakeupList' => [
            'label'=> _('Number of Wake Up Calls in List'),
            'help' => _('Message that informs the number of wake-up calls ({count}) that it has created.'),
            'play' => true,
        ],
		'wakeupListEmpty' => [
            'label'=> _('Empty Wake Up Call List'),
            'help' => _('Message informing you that you have no wake-up call created.'),
            'play' => true,
        ],
		'wakeupListInfoCall' => [
            'label'=> _('Wake Up Call Information'),
            'help' => _('Message that informs us of the details of each wake-up call that is created, when we click on the list option. <br> Available values: {number} for the number occupied by the call in the list and {time} for the date and time of the call.'),
            'play' => true,
        ],
		'wakeupListMenu' => [
            'label'=> _('Options Menu in List of Created Wake-Up Calls'),
            'help' => _('Message that asks us what we want to do after giving us the information of the call. <br> Press 1 to cancel the call, press 2 to continue with the next call or press 3 to go to the menu.'),
            'play' => true,
        ],
		'wakeupListCancelCall' => [
            'label'=> _('Wake Up Call Canceled'),
            'help' => _('Message informing that the wake-up call has been canceled.'),
            'play' => true,
        ],
		'wakeConfirmMenu' => [
            'label'=> _('Snooze Wake-Up Call'),
            'help' => _('Message that does not request if we want you to repeat the call in 5, 10 or 15 minutes.'),
            'play' => true,
        ],
		'wakeConfirmDelay' => [
            'label'=> _('Confirmation of Wake-Up Call Delay'),
            'help' => _('Message confirming that the wake-up call will be repeated in {delay} minutes.'),
            'play' => true,
        ],
    ];

    $msgBoxHelp  = "<p>" . _('To add silence between files we will use <b>"silence|xxx"</b>, <b>xxx</b> corresponding to the number of milliseconds we want the silence to last.') . "</p>";
    $msgBoxHelp .= "<p>" . _('By default when detecting a number "say_number" is used, but if we want to use "say_digits" we will have to use the following format <b>"d|xxxx"</b>, <b>xxx</b> corresponds to the number.') . "</p>";
    $msgBoxHelp .= "<p>" . _('SayUnixTime|xxxx') . "</p>";
?>


<h2><i class="fa fa-language">&nbsp;</i> Edit Message - <?php echo $language . "(".$lang.")" ?></h2>

<?php echo show_help( $msgBoxHelp, _('Wildcards'), false, true, "info"); ?>

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