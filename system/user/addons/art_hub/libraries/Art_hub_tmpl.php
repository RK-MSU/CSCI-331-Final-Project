<?php


class Art_hub_tmpl
{
    public function __construct($param = [])
    {
        ;
    }

    function parse_single_vars(&$tagdata, $vars)
    {
        foreach (ee()->TMPL->var_single as $key => $val)
        {
            if(array_key_exists($key, $vars)) {
                $key_val = $vars[$key];
                $tagdata = ee()->TMPL->swap_var_single($val, $key_val, $tagdata);
            }
        }
    }

}