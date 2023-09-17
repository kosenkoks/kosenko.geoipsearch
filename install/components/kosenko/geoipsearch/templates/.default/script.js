BX.ready(function () {
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

    const showError = function (text) {
        BX.UI.Dialogs.MessageBox.alert(text, (messageBox, button, event) => {
            messageBox.close();
        }
        )
    }

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
                console.log(response);
                if (response.status == "success") {
                    data = response.data;
                    document.querySelector('.result-table .result-ip').innerHTML = data.ip;
                    document.querySelector('.result-table .result-country').innerHTML = data.region ? data.region.name_ru : '';
                    document.querySelector('.result-table .result-region').innerHTML = data.country ? data.country.name_ru : '';
                    document.querySelector('.result-table .result-city').innerHTML = data.city ? data.city.name_ru : '';
                    document.querySelector('.result-table .result-coords').innerHTML = data.city ? data.city.lat + ' ' + data.city.lon : '';
                }
            }, 
            // если возникли ошибки на стороне сервера
            function(response){
                console.log(response.errors);
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
});