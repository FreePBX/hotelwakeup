/**
 * UI Config Global
 * 
 * @author Javier Pastor (VSC55)
 * @license GPLv3
 */

var global_module_hotelwakeup_i18n = {};

function i18nGet(file_name)
{
    var data_return = {};

    if (file_name)
    {
        var post_data = {
            module: 'hotelwakeup',
            command: 'i18n',
            filejs: file_name
        };
        $.ajax({
            async: false,
            type: 'post',
            url: window.FreePBX.ajaxurl,
            data: post_data,
            success: function (data)
            {
                if (data)
                {
                    if (! data.status )
                    {
                        console.log("i18nGet failed:", data.message);
                    }
                    else
                    {
                        data_return = data.i18n;
                    }
                }
                else 
                {
                    console.log("i18nGet failed: No data received!")
                }
            },
            error: function (request, status, error)
            {
                console.log("i18nGet ajax error:", jQuery.parseJSON(request.responseText).Message);
            }
        });
    }

    return data_return;
}

function i18n_mod(find)
{
    find = find.toUpperCase();
	var return_data = _("Not found in i18n!");
	if ( global_module_hotelwakeup_i18n.hasOwnProperty(find) )
	{
		return_data = global_module_hotelwakeup_i18n[find];
	}
	return return_data;
}

function delay(ms, fn) 
{
	let timer = 0
	return function(...args) 
	{
		clearTimeout(timer)
		timer = setTimeout(fn.bind(this, ...args), ms || 0)
	}
}

function sleep(ms)
{
    // Usage: await sleep(10);
    return new Promise(resolve => setTimeout(resolve, ms))
}

function postSyncMode(send_data={}, timeout, dataType)
{
    let data_return = {
        status  : null,
        error   : null,
        getData : {},
    };

    let ajax_args = {
        url: window.FreePBX.ajaxurl,
		async: false,
		type: 'POST',
		data: send_data,
		dataType: dataType,
		timeout: timeout,
		success: function(data)
		{
            data_return.status  = true;
            data_return.getData = data;
		},
		error: function(data) {
            data_return.status = false;
            data_return.error  = data;
            console.log("ERROR AJAX: " + data);
		},
    };
    
    if (typeof(dataType) ==! undefined) {
        ajax_args['dataType'] = dataType;
    }

    if (typeof(timeout) ==! undefined) {
        ajax_args['timeout'] = timeout;
    }

    $.ajax(ajax_args);
    return data_return;
}