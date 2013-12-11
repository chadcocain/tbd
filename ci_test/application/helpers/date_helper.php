<?php
    if( !function_exists('get_month_names'))
    {
        function get_month_names($months)
        {
            $str = '';
    		
            if (!empty($months))
            {
                $parts = explode(',', $months);
        		
                foreach ($parts as $month)
                {
        			$month = (int) $month;
        			$str .= !empty($str) ? ', ' : '';
        			$str .= date('M', mktime(0, 0, 0, $month, 1, 2009));
        		}
            }
    		
            return $str;
    	}
    }
?>