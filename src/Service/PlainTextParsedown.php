<?php
/**
 * Created by PhpStorm.
 * User: SYSTEM
 * Date: 2018/8/3
 * Time: 16:28
 */

namespace App\Service;


class PlainTextParsedown extends \Parsedown
{
    protected function element(array $Element)
    {
        $markup = '';
        if (isset($Element['text']))
        {
            if (isset($Element['handler']))
            {
                $markup .= $this->{$Element['handler']}($Element['text']);
            }
            else
            {
                $markup .= $Element['text'];
            }
        }
        elseif (isset($Element['attributes']['alt']))
        {
            $markup .= $Element['attributes']['alt'];
        }
        return $markup;
    }
}