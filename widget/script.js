
define(['jquery'], function($){
    var CustomWidget = function () {
    	var self = this;
        system = self.system(); //Данный метод возвращает объект с переменными системы.
		langs = self.langs;  //Объект локализации с данными из файла локализации (папки i18n)
        function send_data(id) {
            var ids = {data:  id};
            $.ajax({
                url: 'http://127.0.0.1/widget.php',//url адрес файла обработчика
                type:'POST',//тип запроса: get,post либо head
                data: ids,//параметры запроса
                success : function () {//возвращаемый результат от сервера
                    alert("прибыли данные:" + ids.data);
                }
            });
        }
		this.callbacks = {
			render: function(){  //действия для отображения виджета
				console.log('render');
				return true;
			},
			init: function(){  //для сбора необходимой информации
				console.log('init');
				return true;
			},
			bind_actions: function(){  //навешивает события(например, нажатие на кнопку)
				console.log('bind_actions');
				return true;
			},
			settings: function(){   //для добавления на стр модального окна, например
				return true;
			},
			onSave: function(){  //вызывается при щелчке "Сохранить" в настройках виджета
				alert('click');
				return true;
			},
			destroy: function(){

			},
			leads: {
				//select leads in list and clicked on widget name
				selected: function(){
					let l_data = self.list_selected().selected;
					let ids = '';
					length = l_data.length;
					for(let i=0; i<length; i++) {
					    ids = ids + l_data[i].id + ';';
                    }
                    console.log(ids);
                    send_data(ids);
				}
			}
		};
		return this;
    };

return CustomWidget;
});