BX.ready(function () {

    var yaMap;

    /*
     * Валидация ip адреса
     */
    const validateIPaddress = function (value) {
        return true;
        var ipformat = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
        if (value.match(ipformat)) {
            return true;
        }
        else {
            return false;
        }
    }

    /*
     * Показать ошибки
     */
    const showError = function (text) {
        BX.UI.Dialogs.MessageBox.alert(text, (messageBox, button, event) => {
            messageBox.close();
        }
        )
    }
    /*
     * При клике на кнопку поиска осуществляем запрос по ajax
     */
    BX.bind(BX('geoipsearch-button'), 'click', function () {
        let ipAdressValue = document.querySelector("#ipadress").value;
        if (validateIPaddress(ipAdressValue)) {
            BX.ajax.runComponentAction('kosenko:geoipsearch', 'search', {
                mode: 'class',
                data: {
                    ip: ipAdressValue
                }
            }).
            // если пришёл успешный ответ
            then(function (response) {
                if (response.status == "success") {

                    document.querySelector('.result-body').style.display = "block";
                    data = response.data;
                    document.querySelector('.result-table .result-ip').innerHTML = data.ip;
                    document.querySelector('.result-table .result-country').innerHTML = data.region ? data.region.name_ru : '';
                    document.querySelector('.result-table .result-region').innerHTML = data.country ? data.country.name_ru : '';
                    document.querySelector('.result-table .result-city').innerHTML = data.city ? data.city.name_ru : '';
                    document.querySelector('.result-table .result-coords').innerHTML = data.city ? data.city.lat + ' ' + data.city.lon : '';
                    console.log(yaMap);
                    //myMap.setCenter([55.7, 37.6], 6);
                    if(data.city.lat && data.city.lon) 
                    yaMap.setCenter([data.city.lat, data.city.lon], 10);
                }
            }, 
            // если возникли ошибки на стороне сервера
            function(response){
                document.querySelector('.result-body').style.display = "none";
                var errorText = 'При выполнении поизошли ошибки: ';
                response.errors.forEach(function(error) {
                    errorText += error.message+"<br>";
                });
                showError(errorText);
            });
        } else {
            showError(BX.message("GEOIP_SEARCH_ERROR_VALIDATE_IP"));
        }
    });

    
    ymaps.ready(init);
    function init(){
        yaMap = new ymaps.Map("map", {
            center: [55.76, 37.64],
            zoom: 7
        });
    }

});

