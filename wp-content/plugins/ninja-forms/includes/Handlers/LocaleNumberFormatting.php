<?php

class NF_Handlers_LocaleNumberFormatting
{
    const NBSP = '&nbsp;';

    protected $locale;

    public function __construct($locale) {
        $this->locale = $locale;
    }

    public static function create() {
        global $wp_locale;
        return new self($wp_locale);
    }

    public function locale_decode_number( $number ) {
        // Be sure we have a string.
        $number = strval( $number );

        $thousands_sep = $this->locale->number_format['thousands_sep'];

        // Account for negative numbers.
        $negative = false;
        if ( '-' == substr( $number, 0, 1 ) ) {
            $negative = true;
            $number = str_replace( '-', '', $number );
        }

        // Account for a space as the thousands separator.
        $thousands_sep = str_replace( self::NBSP, ' ', $thousands_sep );
        $number = str_replace( self::NBSP, ' ', $number );

        // Determine what our existing separators are.
        $haystack = str_split( $number );
        $separators = preg_grep( '/[0-9]/', $haystack, PREG_GREP_INVERT );
        $final_separators = array_unique( $separators );
        $final_separators = array_values( $final_separators );
        switch( count( $final_separators ) ) {
            case 0:
                $formatted = $number;
                break;
            case 1:
                $replacer = '';
                if ( 1 == count( $separators ) ) {
                    $separator = reset($separators);
                    list($before, $after) = explode($separator, $number);
               
                    if(3 == strlen($after) && $separator == $thousands_sep) {
                        $replacer = '';
                    } else {
                        $replacer = '.';
                    }
                }
                $formatted = str_replace( $final_separators[0], $replacer, $number );
                break;
            case 2:
                $formatted = str_replace( $final_separators[0], '', $number );
                $formatted = str_replace( $final_separators[1], '.', $formatted );
                break;
            default:
            return 'NaN';
        }

        if ( $negative ) {
            $formatted = '-' . $formatted;
        }

        return $formatted;
    }

    public function locale_encode_number( $number, $decimal = null, $thousand = null ) {
        // Be sure we have a string.
        $number = strval( $number );
        // Decode our input value.
        $number = $this->locale_decode_number( $number );
        // Exit early if NaN.
        if ( 'NaN' == $number ) return 'NaN';

        $thousands_sep = $this->locale->number_format['thousands_sep'];
        $decimal_point = $this->locale->number_format['decimal_point'];

        $thousands_sep = ( !is_null($thousand) ) ? $thousand : $thousands_sep;
        $decimal_point = ( !is_null($decimal) ) ? $decimal : $decimal_point;

        // Account for a space as the thousands separator.
        $thousands_sep = str_replace( ' ', self::NBSP, $thousands_sep );

        $precision = 0;
        if ( false !== strpos( $number, '.' ) ) {
            $tmp = explode( '.', $number );
            $precision = strlen( array_pop( $tmp ) );
        }

        $number = floatval( $number );

        return number_format( $number, $precision, $decimal_point, $thousands_sep );
    }

    public function locale_decode_equation( $eq ) {
        // Be sure we have a string.
        $eq = strval( $eq );
        $result = '';
        $expression = '';
        $pattern = '/[0-9.,]/';
        $eq = str_replace( array('&nbsp;', '&thinsp;', ' '), '', $eq );
        $characters = str_split( $eq );
        foreach ( $characters as $character ) {
            // If it matches the pattern...
            if ( preg_match( $pattern, $character ) ) {
                $expression .= $character;
            } else {
                // If we reach an operator char, append the expression to the result
                if ( 0 < strlen( $expression ) ) {
                    $result .= $this->locale_decode_number( $expression );
                    $expression = '';
                }
                $result .= $character;
            }
        }
        // The following catches the case of the last character being a digit.
        if ( 0 < strlen( $expression ) ) {
            $result .= $this->locale_decode_number( $expression );
        }
        return $result;
    }
}