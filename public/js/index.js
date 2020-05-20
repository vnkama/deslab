/*
файл js скрипты для страницы индекс /index
*/

var calendar;

/**
 * вызыватся из ready()
 */
function pageReady()
{
    //если ссылка на выход сущестует, вешаем обрабочтки
    let elem = document.getElementById('id-logoff');
    if (elem) elem.onclick = idLogoff_onclick;
}

function idLogoff_onclick()
{
    alert('idLogoff_onclick');
}

