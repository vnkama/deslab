/*
файл js скрипты для страницы индекс /index
*/

var calendar;

/**
 * вызыватся из ready()
 */
function pageReady()
{
    document.getElementById('workersTableFilter__curMonthButton').onclick = curMonthButton_onclick;

    calendar  = document.getElementById('workersTableFilter__calendar');
    calendar.onchange = calendar_onchange;

}



/**
 * обработчик кнопки этекущий месяц'
 */
function curMonthButton_onclick()
{
    calendar.value = date2str_ymd(new Date());  //ставим текущий день
    calendar_onchange();
}



/**
 * обработчик изменения календаря
 */
function calendar_onchange()
{
    //alert('calendar_onchange ');  //DEBUG

    //расчитаем начало конец интервала для месяца

    let firstDate = new Date(calendar.value);
    firstDate.setHours(0,0,0);
    firstDate.setDate(1);    //первое число


    let lastDate = new Date(firstDate);
    lastDate.setMonth(firstDate.getMonth() + 1);
    lastDate.setSeconds(lastDate.getSeconds() - 1);

    let arrPostRequest = {
        url: 'index',   //внутренний адрес
        operation: 'getSalaries',
        firstDate:date2str(firstDate),
        lastDate: date2str(lastDate)
    };


    //сформируем POST запрос на новую зарплату
    $.ajax({
        type: 'post',
        url: 'index.php',
        data: arrPostRequest,
        success: postResult_changeMonth,
        dataType: 'json'
    });

    //alert('send ok');     //DEBUG
}



/**
 * вызывается по завершении POST запроса на смену даты
 * @param json
 */

function postResult_changeMonth(jsonPostAnswer)
{
    //alert('postResult_changeMonth');      //DEBUG


    if (jsonPostAnswer['answerResult'] === 'ok'){
        //получен ответ


        let len = jsonPostAnswer['workers'].length;

        //цикл по всем пришедшим рабочим
        for (i=0;i<len;i++) {

            let idWorker = jsonPostAnswer['workers'][i]['id'];
            let fixSalary = jsonPostAnswer['workers'][i]['fixSalary'];
            let bonusSalary = jsonPostAnswer['workers'][i]['bonusSalary'];
            let totalSalary = jsonPostAnswer['workers'][i]['totalSalary'];

            let idRow = 'worker' + idWorker;

            var $elemRow = $('#' + idRow);

            $elemRow.find('.workersTable__fixSalary').text( fixSalary);
            $elemRow.find('.workersTable__bonusSalary').text( bonusSalary);
            $elemRow.find('.workersTable__totalSalary').text( totalSalary);
        }
    }
    else {
        alert('ошибка связи');
    }
}

