Magic select {Beta}
============
Magic form fully utilizes the functionality of https://github.com/kartik-v/yii2-widget-activeform, but set the format view used by MagicModal.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist magicsoft/yii2-form "*"
```

or add

```
"magicsoft/yii2-form": "*"
```

to the require section of your `composer.json` file.

Usage
-----

You can use directly from a form, the widget will dynamically build the selector with dynamic query.

```php
<?php
$form = \magicsoft\form\MagicForm::begin([
    'model' => $model,
    'optionsView' => [
        'title' => 'My form',
        'subTitle' => 'Create'    
    ]
]);

echo $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Name']);

echo $form->field($model, 'attribute_id')->widget(\magicsoft\select\MagicSelect::className(), []);

$form::end();

//This configuration set format;

//If you do not want to set the format, use:
$form = \magicsoft\form\MagicForm::begin([
    'setFormat' => false
]);
//With this configuration the MagicModal no work
?>
```

## License

**MagicForm** is released under the BSD 3-Clause License. See the bundled `LICENSE.md` for details.