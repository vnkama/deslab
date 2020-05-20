//файл JS скриптов для index.html

document.addEventListener('DOMContentLoaded', ready);

function ready()
{
    //если в связанном с данной страницей файле js есть pageRaeady
    // вызовем ее
    if (typeof pageReady === "function") {
        pageReady();
    }
}

function unixtime2str(xTime)
{
    if (!xTime) return 'н/д';
    var d = new Date(xTime);	//xTime - милисек

    var sec = d.getSeconds(); sec = String((sec > 9)? sec :'0'+sec);
    var minn = d.getMinutes(); minn = String((minn > 9)? minn :'0'+minn);
    var hrs = d.getHours();	hrs = String((hrs > 9)? hrs :'0'+hrs);
    var day = d.getDate(); day = String((day > 9)? day :'0'+day);
    var month = d.getMonth()+1;	month = String((month > 9)? month :'0'+month);
    var year = d.getFullYear();

    return day + '.' + month + '.' + year + ' ' + hrs + ':' + minn + ':' + sec;
}


/**
 * выводит дату в формате YYYY-MM-DD HH-MM-SS
 * @param date - дата
 *
 * @returns {string}
 */
function date2str(date)
{
    if (!date) return 'н/д';

    var sec = _date2str_pad2(date.getSeconds())
    var minn = _date2str_pad2(date.getMinutes());
    var hrs = _date2str_pad2(date.getHours());
    var day = _date2str_pad2(date.getDate());
    var month = _date2str_pad2(date.getMonth()+1);
    var year = date.getFullYear();

    return year + '-' + month + '-' + day + ' ' + hrs + ':' + minn + ':' + sec;
}

/**
 * выводит дату в формате YYYY-MM-DD
 * @param date - дата
 *
 * @returns {string}
 */
function date2str_ymd(date)
{
    if (!date) return 'н/д';

    var day = _date2str_pad2(date.getDate());
    var month = _date2str_pad2(date.getMonth()+1);
    var year = date.getFullYear();

    return year + '-' + month + '-' + day;
}


function _date2str_pad2(value)
{
    return String((value > 9)? value :'0'+value);
}
