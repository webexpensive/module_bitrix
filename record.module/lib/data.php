<?php require( $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php' );
/**
 * @class RecordPost
 */
class RecordPost
{
	/**
	 * @var array
	 *
	 * Данные из формы в json
	 */
	protected $data = [];

	/**
	 * @var array
	 *
	 * Данные из формы
	 */
	protected $post_data = [];

	/**
	 * @var int
	 *
	 * ID iblock
	 */
	protected $iblock_id = 8;

	/**
	 * @var boolean
	 *
	 * iblock selection ID
	 */
	protected $iblock_selection_id = false;

	public function __construct()
  	{
		if (!\Bitrix\Main\Loader::includeModule("iblock")) $this->exit_record(array('error' => 7));

		$this->getPostData();
		$this->getDataList();
		$this->getIDUser();
		$this->addRecord();
  	}

  	/**
	 * Получаем данные в json формате из формы
	 */
	protected function getPostData() : void
	{
		$this->data = file_get_contents('php://input');

		if ( empty( $this->data ) ) $this->exit_record(array('error' => 1));

		$this->data = json_decode( $this->data, true );
	}

	/**
	 * Разбираем данные
	 */
	protected function getDataList() : void
	{
		foreach ( $this->data as $value => $key ) {
			$this->post_data[$value] = $key;
		}

		if ( !isset($this->post_data['ras_city']) ) $this->exit_record(array('error' => 3));
		$this->post_data['ras_city'] = filter_var(trim( $this->post_data['ras_city'] ), FILTER_VALIDATE_INT);
		if ( !$this->post_data['ras_city'] ) $this->exit_record(array('error' => 3));

		if ( !isset($this->post_data['ras_specialist']) ) $this->exit_record(array('error' => 5));
		$this->post_data['ras_specialist'] = filter_var(trim( $this->post_data['ras_specialist'] ), FILTER_VALIDATE_INT);
		if ( !$this->post_data['ras_specialist'] ) $this->exit_record(array('error' => 5));

		if ( !isset($this->post_data['ras_date']) ) $this->exit_record(array('error' => 6));
		$this->post_data['ras_date'] = filter_var(trim( $this->post_data['ras_date'] ), FILTER_VALIDATE_REGEXP, array("options" => array("regexp"=>"/^(0[1-9]|[12][0-9]|3[01])[\.](0[1-9]|1[012])[\.](19|20)\d\d$/ui")));
		if ( !$this->post_data['ras_date'] ) $this->exit_record(array('error' => 6));

		if ( !isset($this->post_data['ras_start']) ) $this->exit_record(array('error' => 8));
		$this->post_data['ras_start'] = filter_var(trim( $this->post_data['ras_start'] ), FILTER_VALIDATE_REGEXP, array("options" => array("regexp"=>"/^[0-2]?\d:[0-5]\d$/ui")));
		if ( !$this->post_data['ras_start'] ) $this->exit_record(array('error' => 8));

		if ( !isset($this->post_data['ras_end']) ) $this->exit_record(array('error' => 9));
		$this->post_data['ras_end'] = filter_var(trim( $this->post_data['ras_end'] ), FILTER_VALIDATE_REGEXP, array("options" => array("regexp"=>"/^[0-2]?\d:[0-5]\d/ui")));
		if ( !$this->post_data['ras_end'] ) $this->exit_record(array('error' => 9));

		if ( !isset($this->post_data['ras_step']) ) $this->exit_record(array('error' => 10));
		$this->post_data['ras_step'] = filter_var(trim( $this->post_data['ras_step'] ), FILTER_VALIDATE_INT);
		if ( !$this->post_data['ras_step'] ) $this->exit_record(array('error' => 10));
	}

	/**
	 * Запись в инфоблок
	 */
	protected function addRecord() : void
	{
		$el = new CIBlockElement;

		$PROP = [];
		$PROP[10] = $this->post_data['ras_city'];
		$PROP[11] = $this->post_data['ras_specialist'];
		$PROP[12] = $this->post_data['ras_date'];
		$PROP[13] = $this->post_data['timeng'];
		$PROP[14] = $this->post_data['ras_step'];
		$PROP[15] = $this->post_data['ras_break'];

		$arLoadProductArray = Array(
			"MODIFIED_BY"    => $this->getIDUser(),
			"IBLOCK_SECTION_ID" => $this->iblock_selection_id,
			"IBLOCK_ID"      => $this->iblock_id,
			"PROPERTY_VALUES"=> $PROP,
			"NAME"           => $this->getNameSpecialist().' в '.$this->getNameCity()." на ".$this->post_data['ras_date'],
			"ACTIVE"         => "Y"
		);

		if($PRODUCT_ID = $el->Add($arLoadProductArray)) {

			$this->exit_record(array('error' => 0));
		}
	}

	/**
	 * Получаем ID пользователя
	 */
	protected function getIDUser() : int
	{
		global $USER;

		return $USER->GetID();
	}

	/**
	 * Получаем наименование выбранного города
	 */
	protected function getNameSpecialist() : string
	{
		$element = \Bitrix\Iblock\Elements\ElementSpecialistsTable::getList([
			'select' => ["ID", "IBLOCK_ID","NAME"],
			'filter' => [
				"=ID"=>$this->post_data['ras_specialist'],
				"=ACTIVE"=>"Y"
			],
			'data_doubling' => false,
		])->fetch();

		return $element['NAME'];
	}

	/**
	 * Получаем наименование выбранного специалиста
	 */
	protected function getNameCity() : string
	{
		$element = \Bitrix\Iblock\Elements\ElementCityTable::getList([
			'select' => ["ID", "IBLOCK_ID","NAME"],
			'filter' => [
				"=ID"=>$this->post_data['ras_city'],
				"=ACTIVE"=>"Y"
			],
			'data_doubling' => false,
		])->fetch();

		return $element['NAME'];
	}

  	/**
	 * Возвращаем ответ пользователю
	 */
	protected function exit_record(array $mas) : void
	{
		echo json_encode($mas);
		die();
	}
}

new RecordPost();
?>