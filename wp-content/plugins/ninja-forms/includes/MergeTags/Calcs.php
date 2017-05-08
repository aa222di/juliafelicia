<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class NF_MergeTags_Calcs
 */
final class NF_MergeTags_Calcs extends NF_Abstracts_MergeTags
{
    protected $id = 'calcs';

    protected $_default_group = FALSE;

    public function __construct()
    {
        parent::__construct();
        $this->title = __( 'Calculations', 'ninja-forms' );
        add_filter( 'ninja_forms_calc_setting',  array( $this, 'replace' ) );
    }

    public function __call($name, $arguments)
    {
        return $this->merge_tags[ $name ][ 'calc_value' ];
    }

    public function set_merge_tags( $key, $value, $round = 2 )
    {
        global $wp_locale;
        $dec = $wp_locale->number_format['decimal_point'];
        $sep = $wp_locale->number_format['thousands_sep'];
        $callback = ( is_numeric( $key ) ) ? 'calc_' . $key : $key;

        try {
            $value = str_replace($sep, '', $value);
            $value = str_replace($dec, '.', $value);
            $calculated_value = Ninja_Forms()->eos()->solve( $value );
        } catch( Exception $e ){
            $calculated_value = FALSE;
        }

        $this->merge_tags[ $callback ] = array(
            'id' => $key,
            'tag' => "{calc:$key}",
//            'label' => __( '', 'ninja_forms' ),
            'callback' => $callback,
            'calc_value' => number_format( $calculated_value, $round, $dec, $sep )
        );

        $callback .= '2';

        $this->merge_tags[ $callback ] = array(
            'id' => $key,
            'tag' => "{calc:$key:2}",
//            'label' => __( '', 'ninja_forms' ),
            'callback' => $callback,
            'calc_value' => number_format( $calculated_value, 2, $dec, $sep )
        );
    }
    
    public function get_calc_value( $key )
    {
        return $this->merge_tags[ $key ][ 'calc_value' ];
    }

} // END CLASS NF_MergeTags_Calcs
