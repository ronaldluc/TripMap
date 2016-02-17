<?php
/**
 * @author Ronald Luc
 */

use Nette\Forms\Form;
use Nette\Forms\Controls;

class Helpers
{
    public static function bootstrapForm(Form $form)
    {
        // setup form rendering
        $renderer = $form->getRenderer();
        $renderer->wrappers['controls']['container'] = NULL;
        $renderer->wrappers['pair']['container'] = 'div class="form-group"';
        $renderer->wrappers['pair']['.error'] = 'has-error';
        $renderer->wrappers['control']['container'] = 'div class="col-sm-9"';
        $renderer->wrappers['label']['container'] = 'div class="col-sm-3 control-label"';
        $renderer->wrappers['control']['description'] = 'span class=help-block';
        $renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';

        // make form and controls compatible with Twitter Bootstrap
        $form->getElementPrototype()->class('form-horizontal');
        foreach ($form->getControls() as $control) {
            if ($control instanceof Controls\Button) {
                $control->getControlPrototype()->addClass(empty($usedPrimary) ? 'btn btn-shadow' : 'btn btn-default');
                $usedPrimary = TRUE;
            } elseif ($control instanceof Controls\TextBase || $control instanceof Controls\SelectBox || $control instanceof Controls\MultiSelectBox) {
                $control->getControlPrototype()->addClass('form-control form-control-shadow');
            } elseif ($control instanceof Controls\Checkbox || $control instanceof Controls\CheckboxList || $control instanceof Controls\RadioList) {
                $control->getSeparatorPrototype()->setName('div')->addClass($control->getControlPrototype()->type);
            }
        }
    }

    public static function modifyPolygon($string)
    {
        $output = '[[';
        $counter = 0;
        $iter = 2;



        for ($i = 1; $i < strlen($string); $i++)
        {
            if (($string[$i] == ','))
            {
                if ($counter == 1)
                {
                    $output[$iter] = ']';
                    $output[$iter+1] = ',';
                    $output[$iter+2] = '[';
                    $iter += 3;
                    $counter = 0;
                } else {
                    $output[$iter] = ',';
                    $iter++;
                    $counter = 1;
                }
            } else {
                $output[$iter] = $string[$i];
                $iter++;
            }
        }

        $output[$iter] = ']';

        return $output;
    }
}