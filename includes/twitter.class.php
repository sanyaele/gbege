<?php
class Twitter {
	public function __construct(){	}
	public function searchResults( $search = null ) {
		$url = "http://search.twitter.com/search.atom?q=" . urlencode( $search ) . "&lang=en&rpp=50";
		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		$result = curl_exec( $curl );
		curl_close( $curl );
		$return = new SimpleXMLElement( $result );
		return $return;
		$return = new SimpleXMLElement( $result );
	}
	public function weeklyTrends() {
		$url = "http://search.twitter.com/trends/weekly.json";
		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		$result = curl_exec( $curl );
		curl_close( $curl );
		$return = json_decode( $result, true );
		return $return;
	}
}
?>