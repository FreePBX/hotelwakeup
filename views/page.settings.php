<?php
	$js_files 	 = array();
	$show_page 	 = "";
	$params_page = array();
	
	switch($request['action'])
	{
		case '':
			// if the action is null, set the next case as the default action.
			// NO ADD BRACK TO CASE!!
			$_REQUEST['action'] = "settings";
		
		case "settings":
			$show_page = "settings.settings";
			$js_files[] = "settings.js";
		break;

		case "messages":
			switch($request['option'])
			{
				case '':
					$_REQUEST['option'] = "list";

				case "list":
					$show_page = "settings.message.list";
				break;

				case "edit":
					$list_lang = $hotelwakeup->getLanguages();
					if (empty($request['language']))
					{
						$msgError = _("Warning: No language has been specified!");
						unset($_REQUEST['option']);
					}
					elseif ( ! array_key_exists( $request['language'], $list_lang))
					{
						$msgError = sprintf(_("Warning: The language (%s) is not installed!"), $request['language']);
						unset($_REQUEST['option']);
					}
					else
					{
						$show_page = "settings.message.edit";
						$params_page = array(
							'lang' 		=> $request['language'],
							'language'  => $list_lang[$request['language']],
						);
						$js_files[] = "messages.js";
					}
				break;

				default:
					$msgError = sprintf(_("Option Not Found (%s)!!!!"), $request['option']);
				break;
			}
		break;

		default:
			$msgError = sprintf(_("Action Not Found (%s)!!!!"), $request['action']);
		break;
	}

?>

<h1><?php echo _("Hotel Style Wakeup Calls"); ?></h1>
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12">
			<div class="fpbx-container">
			<?php 
				if (! empty($msgError) ) { echo sprintf('<div class="alert alert-warning" role="alert">%s</div>', $msgError); }
				if (! empty($show_page)) { echo $hotelwakeup->showPage($show_page, $params_page); }
				foreach($js_files as $js_file)
				{
					if (empty($js_file)) { continue; }
					echo sprintf('<script type="text/javascript" src="modules/hotelwakeup/assets/js/views/%s"></script>', $js_file);
				}
			?>
			</div>
		</div>
	</div>
</div>
