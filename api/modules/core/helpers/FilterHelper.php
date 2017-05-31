<?php

namespace api\modules\core\helpers;

use Yii;
use yii\helpers\Html;

/**
 * Description of FilterHelper
 *
 * @author Stableflow
 */
class FilterHelper {

    public static function dateRange($searchModel, $attributeFrom, $attributeTo, $format = 'yyyy-mm-dd') {
        $modelName = explode('\\', get_class($searchModel));
        $inputFrom = Html::textInput(end($modelName) . "[$attributeFrom]", $searchModel->{$attributeFrom}, ['class' => 'form-control', 'readonly' => '', 'placeholder' => Yii::t('app', 'From')]);
        $inputTo = Html::textInput(end($modelName) . "[$attributeTo]", $searchModel->{$attributeTo}, ['class' => 'form-control', 'readonly' => '', 'placeholder' => Yii::t('app', 'To')]);
        return "<div class = \"input-group date date-picker\" data-date-format = \"yyyy-mm-dd\">
                    $inputFrom
                    <span class = \"input-group-btn\">
                        <button class = \"btn default date-set\" type = \"button\"><i class = \"fa fa-calendar\"></i></button>
                    </span>
                </div><br/>
                <div class = \"input-group date date-picker\" data-date-format = \"yyyy-mm-dd\">
                    $inputTo
                    <span class = \"input-group-btn\">
                        <button class = \"btn default date-set\" type = \"button\"><i class = \"fa fa-calendar\"></i></button>
                    </span>
                </div>";
    }
    
}
