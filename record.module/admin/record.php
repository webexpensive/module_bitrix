<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once __DIR__ . '/../include.php';
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

use \Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('ACU_RECORD_MENU_NAME'));
$module_id = 'record.module';
CModule::IncludeModule($module_id);

if (\Bitrix\Main\Loader::includeModule('iblock')) {

	$elements = \Bitrix\Iblock\Elements\ElementCityTable::getList([
		'select' => ["ID", "IBLOCK_ID","NAME"],
		'filter' => [
			"=ACTIVE"=>"Y"
		],
		'data_doubling' => false,
	])->fetchAll();

	$mas_option_city = '';

	foreach ($elements as $element) {
		$mas_option_city .= '<option value="'.$element['ID'].'">'.$element['NAME'].'</option>';
	}

	$elements = \Bitrix\Iblock\Elements\ElementSpecialistsTable::getList([
		'select' => ["ID", "IBLOCK_ID","NAME"],
		'filter' => [
			"=ACTIVE"=>"Y"
		],
		'data_doubling' => false,
	])->fetchAll();

	$mas_option_specialists = '';

	foreach ($elements as $element) {
		$mas_option_specialists .= '<option value="'.$element['ID'].'">'.$element['NAME'].'</option>';
	}

}
?>
<link rel="stylesheet" href="/local/templates/trend/assets/css/jquery-ui.min.css">
<link rel="stylesheet" href="/local/templates/trend/assets/css/toast.css">
<style>
	#load_button_final {
		display:none;
	}
</style>
<script src="/local/templates/trend/assets/js/jquery-3.5.1.slim.min.js"></script>
<script src="/local/templates/trend/assets/js/jquery-ui.min.js"></script>
<script src="/local/templates/trend/assets/js/jquery.inputmask.js"></script>
<script src="/local/templates/trend/assets/js/toast.js"></script>

<form class="form-inline" id="ras_form" name="ras_form"><div class="adm-detail-content-wrap">
    <div class="adm-detail-content" id="edit1"><div class="adm-detail-title">Добавить время записи</div>
		<div class="adm-detail-content-item-block">

			<table class="adm-detail-content-table edit-table" id="edit1_edit_table">
				<tbody>
					<tr>
						<td width="40%" class="adm-detail-content-cell-l">Выберите город:</td>
						<td class="adm-detail-content-cell-r">
							<select class="nice-select wide" id="ras_city" name="ras_city" required>
								<option value="0" selected>Выберите...</option>
								<?=$mas_option_city?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="adm-detail-content-cell-l">Выберите специалиста:</td>
						<td class="adm-detail-content-cell-r">
							<select class="nice-select wide" id="ras_specialist" name="ras_specialist" required>
								<option value="0" selected>Выберите...</option>
								<?=$mas_option_specialists?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="adm-detail-content-cell-l">Дата</td>
						<td class="adm-detail-content-cell-r">
							<input type="text" class="form-control" id="ras_date" name="ras_date" placeholder="Выберите дату" required>
						</td>
					</tr>
					<tr>
						<td class="adm-detail-content-cell-l">Начало рабочего дня</td><td class="adm-detail-content-cell-r">
							<input type="text" class="form-control" id="ras_start" name="ras_start" placeholder="Введите время" required value="09:00">
						</td>
					</tr>
					<tr>
						<td class="adm-detail-content-cell-l">Окончание рабочего дня</td><td class="adm-detail-content-cell-r">
							<input type="text" class="form-control" id="ras_end" name="ras_end" placeholder="Введите время" required value="18:00">
						</td>
					</tr>
					<tr>
						<td class="adm-detail-content-cell-l">Шаг временных интервалов в минутах</td><td class="adm-detail-content-cell-r">
							<input type="text" class="form-control" id="ras_step" name="ras_step" placeholder="Введите время" required value="15">
						</td>
					</tr>
					<tr>
						<td class="adm-detail-content-cell-l">Перерыв</td><td class="adm-detail-content-cell-r">
							<input type="text" class="form-control" id="ras_break" name="ras_break" placeholder="Введите время" value="13:00-14:00">
						</td>
					</tr>
				</tbody>
			</table>

		</div>
	</div>
	<div class="adm-detail-content-btns">
		<div id="load_button_nextstep"><input type="submit" value="Продолжить"></input></div>
	</div>
	<div class="book-time" id="book-times"></div>
	<div class="adm-detail-content-btns">
		<div id="load_button_final"><input type="submit" class="adm-btn-save" value="Создать время записи"></input></div>
	</div>
</form>
<script src="/local/modules/record.module/js/script.js"></script>

<?require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>