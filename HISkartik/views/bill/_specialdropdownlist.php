<?php
	/*args
		$model
		$field
		$url ~ to refresh content 
		$default_data ~ [id, text] or []
		$templateResult  ~ default to 'function(model_row) { return model_row.id + " (" + model_row.text + ")"; }'
		$templateSelection ~ default to  'function (model_row) { return model_row.id + " (" + model_row.text + ")"; }'
	*/
	echo $form->field($model, $field)->widget(Select2::classname(), [
		'data' => $default_data,//ArrayHelper::map($data, 'status_code', 'status_description'),
		'options' => ['placeholder' => 'Search for a kod taraf ...'],
		'pluginOptions' => [
			'minimumInputLength' => 2,
			'language' => [
				'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
			],
			'ajax' => [
				'url' => $url, //\yii\helpers\Url::to(['load-kod-taraf']),
				'dataType' => 'json',
				'data' => new JsExpression('function(params) { return {q:params.term}; }'),
			],
			'escapeMarkup' => new JsExpression('function (model_row) { return model_row; }'),
			'templateResult' => new JsExpression(empty($templateResult)? 'function(model_row) { return model_row.id + " (" + model_row.text + ")"; }': $templateResult),
			'templateSelection' => new JsExpression(empty($templateSelection)?'function (model_row) { return model_row.id + " (" + model_row.text + ")"; }': $templateSelection),
		],
	]);
?>