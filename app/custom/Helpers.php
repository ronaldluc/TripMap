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

    public static function rgb2html($r, $g=-1, $b=-1)
    {
        if (is_array($r) && sizeof($r) == 3)
            list($r, $g, $b) = $r;

        $r = intval($r); $g = intval($g);
        $b = intval($b);

        $r = dechex($r<0?0:($r>255?255:$r));
        $g = dechex($g<0?0:($g>255?255:$g));
        $b = dechex($b<0?0:($b>255?255:$b));

        $color = (strlen($r) < 2?'0':'').$r;
        $color .= (strlen($g) < 2?'0':'').$g;
        $color .= (strlen($b) < 2?'0':'').$b;

        return '#'.$color;
    }
}