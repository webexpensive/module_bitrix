window.form_ras_level = 0;

	function splitInterval( start, end, step, break_date ) {
		let result = [];

		for ( let ts = start; ts < end; ts += step ) {
			if ( ( ts == break_date[0] ) || ( ts > break_date[0] && ts < break_date[1] )  ) continue;
			result.push(ts);
		}

		if ( result.length == 1 ) result.push( end );

		return result;
	}

	document.addEventListener('DOMContentLoaded', function() {

	    $.datepicker.regional['ru'] = {
			closeText: 'Закрыть',
			prevText: 'Предыдущий',
			nextText: 'Следующий',
			currentText: 'Сегодня',
			monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
			monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'],
			dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
			dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
			dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
			weekHeader: 'Не',
			dateFormat: 'dd.mm.yy',
			firstDay: 1,
			isRTL: false,
			showMonthAfterYear: false,
			yearSuffix: ''
		};
		$.datepicker.setDefaults($.datepicker.regional['ru']);

	    $( "#ras_date" ).datepicker({
			minDate: 0
		});

		$('#ras_start').mask('99:99');
		$('#ras_end').mask('99:99');
		$('#ras_step').mask('99');
		$('#ras_break').mask('99:99-99:99');

	  });

	var s_list = [];
		s_list[1] = "Неверный запрос";
		s_list[2] = "Ошибка запроса";
		s_list[3] = "Не выбран город";
		s_list[4] = "Ошибка данных";
		s_list[5] = "Не выбран специалист";
		s_list[6] = "Не выбрана дата";
		s_list[7] = "Ошибка. Обратитесь к администрации сайта";
		s_list[8] = "Укажите начало рабочего дня";
		s_list[9] = "Укажите окончание рабочего дня";
		s_list[10] = "Задайте интервал";

	ras_form.onsubmit = async (e) => {
		e.preventDefault();

		if (window.form_ras_level == 0) {

			let ras_date = document.getElementById('ras_date').value,
				ras_start = document.getElementById('ras_start').value,
				ras_end = document.getElementById('ras_end').value,
				ras_step = document.getElementById('ras_step').value,
				ras_break = document.getElementById('ras_break').value,
				new_date = ras_date.split('.'),
				break_date = ras_break.split('-'),
				html_book_time = '';

			if ( document.getElementById('ras_city').value == 0 ) {
				new a_toast({
					title: 'Ошибка',
					text: 'Выберите город',
					theme: 'danger',
					autohide: true,
					interval: 10000
				});
				return false;
			}

			if ( document.getElementById('ras_specialist').value == 0 ) {
				new a_toast({
					title: 'Ошибка',
					text: 'Выберите специалиста',
					theme: 'danger',
					autohide: true,
					interval: 10000
				});
				return false;
			}

			for ( let i = 2; i--; ) new_date[i] = ("0" + new_date[i]).slice(-2);

			new_date = new_date.reverse().join('-');

			let start_date = new_date+'T'+ras_start+':00',
				end_date = new_date+'T'+ras_end+':00';

			start_date = new Date(start_date).valueOf();
			end_date = new Date(end_date).valueOf();

			ras_step  = ras_step * 60 * 1000;

			if ( break_date ) {

				break_date[0] = new_date+'T'+break_date[0]+':00',
				break_date[1] = new_date+'T'+break_date[1]+':00';

				break_date[0] = new Date(break_date[0]).valueOf();
				break_date[1] = new Date(break_date[1]).valueOf();

			}

			let result = splitInterval(start_date, end_date, ras_step, break_date);
			window.form_ras_lines_time = [];

			for (let i = 0; i < result.length; ++i) {

				let full_time = (new Date(result[i])).toLocaleString(),
					line_time = full_time.split(', ');

				html_book_time += '<tr class="adm-list-table-row"><td class="adm-list-table-cell adm-list-table-checkbox adm-list-table-checkbox-hover">'+full_time+'</td></tr>';

				window.form_ras_lines_time[i] = line_time[1];

			}

			document.getElementById('load_button_nextstep').style.display = 'none';
			document.getElementById('book-times').innerHTML = '<table class="adm-list-table" id="tbl_record_group"><thead><tr class="adm-list-table-header"><td class="adm-list-table-cell adm-list-table-cell-sort"><div class="adm-list-table-cell-inner">Предварительный просмотр</div></td></tr></thead><tbody>'+html_book_time+'</tbody></table>';
			document.getElementById('load_button_final').style.display = 'block';

			window.form_ras_level = 1;


		} else {

			let formData = new FormData(ras_form);
			formData.append("timeng", JSON.stringify(window.form_ras_lines_time));

			let response = await fetch('/local/modules/record.module/lib/data.php', {
				method: 'POST',
				headers: {
				  'Accept': 'application/json',
				  'Content-Type': 'application/json'
				},
				body: JSON.stringify(Object.fromEntries(formData))
			});

			let result = await response.json();

			if ( result.error != 0 ) {
				alert_error(result.error);
			} else {
				window.form_ras_level = 0;
				document.getElementById('load_button_nextstep').style.display = 'block';
				document.getElementById('book-times').innerHTML = '';
				document.getElementById('load_button_final').style.display = 'none';
				new a_toast({
					title: 'Успешно',
					text: 'Время записи добавлено',
					theme: 'success',
					autohide: true,
					interval: 10000
				});
			}

		}

	};

	function alert_error(id_error)
	{
		new a_toast({
			title: 'Ошибка',
			text: s_list[id_error],
			theme: 'danger',
			autohide: true,
			interval: 10000
		});
	}