<?php
abstract class SearchCli {
	abstract public function startSearch($word);
}

class MockSearchCli extends SearchCli {
	public function startSearch($word) {
		return "http://www.google.com";
	}
}