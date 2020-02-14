<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_PromotionManager
{

    public $promotions;

    /**
     * Accepts a string of location to tell let us know where in the app we are sending promotions to. 
     * Then will return an array of promotions to run in the location. 
     */
    public function __construct()
    {
        $this->set_promotions();
        $this->maybe_remove_personal();
        $this->maybe_remove_ninja_shop();
        $this->maybe_remove_sendwp();
        $this->sort_active_promotions_by_locations();
    }

    public function get_promotions()
    {
        return $this->promotions;   
    }

    /**
     * Set our promtions array to our promotions property. 
     */
    private function set_promotions()
    {
        if ( apply_filters( 'ninja_forms_disable_marketing', false ) ) 
        {
            $this->promotions = array();
        } else {
            $this->promotions = Ninja_Forms()->config( 'DashboardPromotions' );
        }
    }

    /**************************************************************************
     * Membership Checks
     * 
     * These funcitons all check to see if the individual add-ons that make up
     * our personal membership are active. 
    ****************************************************************************/
    private function is_layout_styles_active()
    {
        return class_exists( 'NF_Layouts', false );
    }
    
    private function is_conditional_logic_active()
    {
        return class_exists( 'NF_ConditionalLogic', false );
    }

    private function is_multi_part_active()
    {
        return class_exists( 'NF_MultiPart', false );
    }

    private function is_file_uploads_active()
    {
        return class_exists( 'NF_FU_File_Uploads', false );
    }

    /**
     * Utilizes the helper methods above to determine if a
     * a Membership is active on a site. 
     */
    private function is_personal_active()
    {
        if( $this->is_conditional_logic_active() && $this->is_file_uploads_active() &&
            $this->is_layout_styles_active() && $this->is_multi_part_active() ) {
                return true; 
        }
        return false;
    }

    /**************************************************************************
     * Promotion Removal Methods
     * 
     * These funcitons all check for different add-ons/products and remove
     * promotions for them if they are in use. 
    ****************************************************************************/
    private function maybe_remove_sendwp()
    {
        if( phpversion() < '5.6.0' ) {
            $this->remove_promotion( 'sendwp' ); 
            return; 
        } if( $this->is_sendwp_active() ) {
            $this->remove_promotion( 'sendwp' );
        } elseif( $this->is_ninja_mail_active() ) {
            $this->remove_promotion( 'sendwp' );
        }
    }

    private function maybe_remove_ninja_shop()
    {
        if( ( ! $this->are_product_fields_in_use() && ! $this->are_calculations_in_use() ) || $this->is_ninja_shop_active() ) {
            $this->remove_promotion( 'ninja-shop' );
        }
    }

    private function maybe_remove_personal() 
    {
        if( $this->is_personal_active() ) {
            $this->remove_promotion( 'personal' );
        }
    }

    /***************************************************************************
     * Helper Methods 
    ****************************************************************************/
    /**
     * Pass in a promotion type to have it removed from 
     * the list of active promotions.
     * 
     * @return void 
     */
    private function remove_promotion( $type )
    {
        // Loops over promotions and removes unused types of promotions. 
        foreach( $this->promotions as $promotion ) {
            if( $type == $promotion[ 'type' ] ) {
                unset( $this->promotions[ $promotion[ 'id' ] ] );
            }
        }
    }

    /**
     * Sorts our promotions by where they will appear in app.
     *
     * @return void 
     */
    private function sort_active_promotions_by_locations()
    {
        $sorted_locations = array();
        foreach( $this->promotions as $promotion ) {
            $sorted_locations[ $promotion[ 'location' ] ][] = $promotion;
        }
        $this->promotions = $sorted_locations;
    }

    /**
     * Checks the DB to see if product fields are being used. 
     */
    private function are_product_fields_in_use()
    {
        global $wpdb;

        $query = "SELECT id FROM `" . $wpdb->prefix . "nf3_fields` WHERE type = 'product'"; 
        $fields = $wpdb->get_results( $query, 'ARRAY_A' ); 
        
        if( ! empty( $fields ) ) {
            return true; 
        }
        return false; 
    }

    private function are_calculations_in_use()
    {
        global $wpdb;

        // TODO: change the key to meta_key once DB changes have been fully implemented. 
        $query = "SELECT count( id ) as total FROM `" . $wpdb->prefix . "nf3_form_meta` WHERE  `key` = 'calculations' AND value <> 'a:0:{}'"; 
        $calcs = $wpdb->get_row( $query, 'ARRAY_A' ); 

        if( $calcs[ 'total' ] > 0 ) {
            return true; 
        }
        return false;
    }

    private function is_ninja_shop_active()
    {
        if( class_exists( 'IT_Exchange', FALSE ) ) {
            return true;
        } 
        return false;
    }

    private function is_sendwp_active()
    {
        if( class_exists( '\SendWP\Mailer', FALSE ) ) {
            return true; 
        }
        return false; 
    }

    private function is_ninja_mail_active()
    {
        if( class_exists('\NinjaMail\Plugin', FALSE ) ) {
            return true;
        }
        return false;
    }
}