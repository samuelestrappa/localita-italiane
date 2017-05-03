<?php 
class LocalitaItaliane
{

	public function __construct(){
		add_action('wp_ajax_resocosp_cerca_comuni', array($this, 'cerca_comuni'));
		add_action('wp_ajax_resocosp_lista_province', array($this, 'lista_province'));
		add_action('wp_ajax_nopriv_resocosp_lista_province', array($this, 'lista_province'));
		add_action('wp_ajax_resocosp_lista_comuni_per_provincia', array($this, 'lista_comuni_per_provincia'));
		add_action('wp_ajax_nopriv_resocosp_lista_comuni_per_provincia', array($this, 'lista_comuni_per_provincia'));

	}

	public static function activation(){
		if(!LocalitaItaliane::check_table_exists()){
			LocalitaItaliane::create_table();
		}
	}

	public static function deactivation(){}

	private static function check_table_exists(){
		global $wpdb;
		$table_name = $wpdb->prefix . 'localita_italiane';
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) return false;
		else return true;
	}

	private static function create_table(){
		global $wpdb;

		$table_name = $wpdb->prefix . 'localita_italiane';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
				id int(4) NOT NULL AUTO_INCREMENT,
				regione varchar(30),
				provincia varchar(30),
				sigla_provincia varchar(2),
				comune varchar(50),
				PRIMARY KEY (id)
				) $charset_collate";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta( $sql );

		LocalitaItaliane::inserisci_dati();
	}

	private static function inserisci_dati(){

		global $wpdb;

		$file_content = file_get_contents( LOCALITAITALIANE_PLUGIN_DIR . 'localita-italiane.json');

		$data = json_decode($file_content, true);

		$table_name = $wpdb->prefix . 'localita_italiane';
		foreach ($data as $value) {

			$wpdb->insert(
				$table_name, 
				array('regione' => $value['regione'],
					  'provincia' => $value['provincia'],
					  'sigla_provincia' => $value['sigla_provincia'],
					  'comune' => $value['comune']	
				));
		}
	}

	public static function get_row_by_id($id){
		global $wpdb;
		$table_name = $wpdb->prefix . 'localita_italiane';
		$query = "SELECT * FROM ".$table_name." WHERE id = ".$id;
		$results = $wpdb->get_results( $query , ARRAY_A);
		return $results;
	}

	public static function get_provincia_by_comune($comune){
		global $wpdb;
		$table_name = $wpdb->prefix . 'localita_italiane';
		$row = $wpdb->get_results( "SELECT provincia, sigla_provincia FROM ".$table_name." WHERE comune = '".$comune."'", ARRAY_A );
		return $row;
	}

	public static function get_comuni_by_provincia($sigla){
		global $wpdb;
		$table_name = $wpdb->prefix . 'localita_italiane';
		$query = "SELECT id, comune FROM ".$table_name." WHERE sigla_provincia = '".$sigla."' ORDER BY comune";
		$results = $wpdb->get_results( $query , ARRAY_A);
		return $results;
	}

	public static function get_lista_province(){
		global $wpdb;
		$table_name = $wpdb->prefix . 'localita_italiane';
		$query = "SELECT DISTINCT provincia, sigla_provincia FROM ".$table_name." ORDER BY provincia";
		$results = $wpdb->get_results( $query , ARRAY_A);
		return $results;
	}

	public function lista_province(){

		$response["response"] = LocalitaItaliane::get_lista_province();

		echo json_encode($response);
		die();
	}

	public function lista_comuni_per_provincia(){

		$response["response"] = LocalitaItaliane::get_comuni_by_provincia($_GET['sigla']);

		echo json_encode($response);
		die();
	}


	public function cerca_comuni($sigla_provincia){
		$provincia = $_POST['siglaProvincia'];

		$response = LocalitaItaliane::get_comuni_by_provincia($provincia);
		echo json_encode($response);
		die();
	}

}
?>