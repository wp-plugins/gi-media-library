<?php
defined( 'ABSPATH' ) OR exit;
/**
 * Description of GIML_View
 *
 * @author Zishan J.
 */
class GIML_View {

    /**
     * Path of the view to render
     */
    var $view = "";

    /**
     * Variables for the view
     */
    var $vars = array();

    /**
     * Construct a view from a file in the 
     */
    public function __construct($view) {
        if (file_exists(GIML_INCLUDES . "views/" . $view . ".php")) {
            $this->view = GIML_INCLUDES . "views/" . $view . ".php";
        } else {
            wp_die(__("View " . GIML_INCLUDES . "views/" . $view . ".php" . " not found"));
        }
    }

    /**
     * set a variable which gets rendered in the view
     */
    public function set($name, $value) {
        $this->vars[$name] = $value;
    }

    /**
     * render the view
     */
    public function render($echo = true) {
        extract($this->vars, EXTR_SKIP);
        ob_start();
        include $this->view;
        if ($echo){
            echo ob_get_clean();
        }else{
            return ob_get_clean();
        }
    }

}

?>
