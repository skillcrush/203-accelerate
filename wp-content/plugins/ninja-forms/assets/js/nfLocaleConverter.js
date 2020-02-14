// const Intl = require('intl');

// class nfLocaleConverter {
var nfLocaleConverter = function(newLocale, thousands_sep, decimal_sep) {

    // constructor(newLocale = 'en-US', thousands_sep, decimal_sep) {
        if ('undefined' !== typeof newLocale && 0 < newLocale.length) {
            this.locale = newLocale.replace('_','-');
        } else {
            this.locale = 'en-US';
        }

        this.thousands_sep = thousands_sep || ',';
        this.decimal_sep = decimal_sep || '.';
    // }

    this.uniqueElememts = function( value, index, self ) {
        return self.indexOf(value) === index;
    }

    this.numberDecoder = function(num) {
        num = num.toString();
        // let thousands_sep = ',';
        var formatted = '';

        // Account for negative numbers.
        var negative = false;
        
        if ('-' === num.charAt(0)) {
            negative = true;
            num = num.replace( '-', '' );
        }
        
        // Account for a space as the thousands separator.
        // This pattern accounts for all whitespace characters (including thin space).
        num = num.replace( /\s/g, '' );
        num = num.replace( /&nbsp;/g, '' );

        // Determine what our existing separators are.
        var myArr = num.split('');
        var separators = myArr.filter(function(el) {
            return !el.match(/[0-9]/);
          });
          
        var final_separators = separators.filter(this.uniqueElememts);
        
        switch( final_separators.length ) {
            case 0:
                formatted = num;
                break;
            case 1:
                var replacer = '';
                if ( 1 == separators.length ) {
                    separator = separators.pop();
                    var sides = num.split(separator);
                    var last = sides.pop();
                    if ( 3 == last.length && separator == this.thousands_sep ) {
                        replacer = '';
                    } else {
                        replacer = '.';
                    }
                } else {
                    separator = final_separators.pop();
                }

                formatted = num.split(separator).join(replacer);
                break;
            case 2:
                var find_one = final_separators[0];
                var re_one;
                if('.' === find_one) {
                    re_one = new RegExp('[.]', 'g');
                } else {
                    re_one = new RegExp(find_one, 'g');
                }
                formatted = num.replace(re_one, '');
                
                var find_two = final_separators[1];
                
                var re_two;
                if('.' === find_two) {
                    re_two = new RegExp('[.]', 'g');
                } else {
                    re_two = new RegExp(find_two, 'g');
                }
                formatted = formatted.replace(re_two, '.' );
                break;
            default:
            return 'NaN';
        }

        if ( negative ) {
            formatted = '-' + formatted;
        }
        this.debug('Number Decoder ' + num + ' -> ' + formatted );
        return formatted;
    }

    this.numberEncoder = function(num, percision) {
        num = this.numberDecoder(num);

        return Intl.NumberFormat(this.locale, { minimumFractionDigits: percision, maximumFractionDigits: percision }).format(num);
    }

    this.debug = function(message) {
        if ( window.nfLocaleConverterDebug || false ) console.log(message);
    }
}

// module.exports = nfLocaleConverter;