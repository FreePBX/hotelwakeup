<h2>Edit Message <?php echo " - " . $language . "(".$lang.")" ?></h2>
<div class="alert alert-info" role="alert">
    Para añadir un silencio entre archivos usaremos "silence|xxx", correspondiendo xxx al número de milisegundos que deseamos que dure el silencio.<br>
    SayUnixTime|xxxx<br>
    d|xxx<br>
</div>
<div class="display full-border">
    <form id="messageform">
        <?php
        foreach( $hotelwakeup->getMessageAll($request['language']) as $key => $val )
        {
            $data = array(
                "hotelwakeup" => $hotelwakeup,

                'key'   => $key,
                'value' => implode(",", $val),
                'title' => ucfirst($key),
                'help'  => '',
            );

            switch($key)
            {
                default:
                    $data['help'] = "";
            }

            echo $hotelwakeup->showPage('settings.message.edit.line', $data);
        }
        ?>
    </form>
</div>





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