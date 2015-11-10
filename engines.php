<?php
abstract class SearchEngine{
	abstract public function autocomplete($word);
	abstract public function search($searchString);
}

class MockEngine extends SearchEngine {
	public function autocomplete($word) {
		return array(
			"Hej",
			"Hoj",
			"Hejdå"
		);
	}
	public function search($searchString) {
		return array(
			0 => array(
				'title' => "Wikipedie",
				'desciption' => "Uppslagsvärk",
				'url' => 'www.wikipedia.com'
			),
			1 => array(
				'title' => 'Google',
				'desciption' => 'Sökmotor',
				'url' => 'www.google.se'
			)
		);
	}
}


class Google extends SearchEngine {
	private $autocompleteBackend = "https://www.google.se/s?sclient=psy-ab&q=";
	private $searchBackend = "https://www.google.se/search?sclient=psy-ab&q=";
	private $autocompleteParams = "-H 'accept-encoding: gzip, deflate, sdch' -H 'accept-language: sv-SE,sv;q=0.8,en-US;q=0.6,en;q=0.4' -H 'accept: */*' -H 'referer: https://www.google.se/' -H 'authority: www.google.se' --compressed";
	private $searchParams = "-H 'accept-encoding: gzip, deflate, sdch' -H 'accept-language: sv-SE,sv;q=0.8,en-US;q=0.6,en;q=0.4' -H 'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.80 Safari/537.36' -H 'accept: */*' -H 'referer: https://www.google.se/' -H 'avail-dictionary: mpK3nL2B' -H 'authority: www.google.se'  --compressed";

	public function autocomplete($word) {
		$cmd = "curl '" . $this->autocompleteBackend . urlencode($word) . "' " . $this->autocompleteParams;
		$json = json_encode(shell_exec($cmd), true);
		$strings = preg_replace('%(<b>|</b>)%', '', array_column($json[1], 0));
		return $strings;
	}

	public function search($searchString) {
		$cmd = "curl '" . $this->searchBackend . urlencode($searchString) . "' " . $this->searchParams;
		$res = shell_exec($cmd);
		preg_match_all('%<div\s*class="g">\s*<!--m-->.*<!--n-->\s*</div>%Usi', $res, $slices, PREG_SET_ORDER);
		return array_map(
			function($slice) {
				preg_match('%href="([^"]*)"[^>]*>([^<]*)<.*class="st"[^>]*>(?:\s*<[^>]*>)*?\s*(.*)$%Usi', $slice[0], $site);
				unset($site[0]);
				return array_map('html_entity_decode', $site);
			}, 
			$slices
		);
	}
}
