 <?php

/////////////////////////
	class termuxSearch {
		private $debug = false;
		private $query = null;
		////////
		private function dbg($what){
			if($this->debug)
				echo $what;
		}
		private function cmdexe($what, $oknok = false){
			if ($oknok === true){
				$o = trim(`$what && echo ok || echo nok`);
			}else{
				$o = trim(`$what`);
			}
			return $o;
		}
		public function __construct($engine=null, $what=null, $oFormat="json"){
			
		}

		public function addSearchTerms($term){
			$term = trim($term, "+ ");
			$this->query .= "+$term";
		}
		
		public function resetSearchTerms(){
			$this->query = null;
		}

		public function google($num=10, $start=0, $ssl=true){
			$o = $this->sURI("www.google.com/search?q=", $this->query, "&num=$num&start=$start&safe=off", $ssl);
			return $o;
		}
		public function duckduckgo($num=10, $start=0, $ssl=true, $json=true){
			$o = $this->sURI("api.duckduckgo.com/?q=", $this->query, "&format=json&n=$num&s=$start&t=termux-cute-search-dev", $ssl);
			return $o;
		}

		private function sURI($uri, $query, $opts=null, $ssl=true){
			if (strlen ($this->query) < 2)
				return false;
			$proto = ( $ssl === true ) ? "https" : "http"; 
			$c = $proto."://".$uri.$query.$opts;
			$this->dbg("sURI:".$c);
			return $c;
		}
		
	}
/////////////////////////


DEFINE("MAN", "
		Command to call a contact searching by name
		it's like this but easier to remember
			termux-contact-list | tr '[:upper:]' '[:lower:]'\
					| jq -c '.[] | select(.name | contains(\"john\"))'\
					| jq -s unique | jq -c '.[] | select(.name | contains(\"doe\"))' ... so on
	Usage:
				search -p <keyword1> [keyword2] ...
					Search the Play Store for app named keyword1...n
				search -x <keyword1> [keyword2] ... {*default}
					Search searx.me for keyword1...n
				search -g <keyword1> [keyword2] ...
					Search google for keyword1...n
				search -h <keyword1> [keyword2] ...
					Search hackernews for keyword1...n
				search -b <keyword1> [keyword2] ...
				search --pirateBay <keyword1> [keyword2] ...
					Search piratebay for keyword1...n
				search -y <keyword1> [keyword2] ...
					Search yts for keyword1...n
				
");

	$mySearch = new termuxSearch();
	$mySearch->addSearchTerms("hola");
	$mySearch->addSearchTerms("php");
	$t[] = $mySearch->google();
	$t[] = $mySearch->duckduckgo();
	print_r($t);

exit;
	require __DIR__ ."/../vendor/autoload.php";
	$opts = new Commando\Command();

	$opts->option('m')->aka('man')->describedAs(MAN)->boolean()->defaultsTo(false);
	$opts->option('p')->aka('playstore')->describedAs('Search the play store for keywords')->boolean()->defaultsTo(false);
	$opts->option('x')->aka('searxme')->describedAs('use searx.me for keywords')->boolean()->defaultsTo(false);
	$opts->option('g')->aka('google')->describedAs('Search google for keywords')->boolean()->defaultsTo(false);
	$opts->option('d')->aka('duck')->describedAs('Search duck duck go for keywords')->boolean()->defaultsTo(false);
	$opts->option('n')->aka('hackernews')->describedAs('Search Hacker News for keywords')->boolean()->defaultsTo(false);
	$opts->option('b')->aka('piratebay')->describedAs('Search the Pirate Bay (always on terminal)')->boolean()->defaultsTo(false);
	$opts->option('y')->aka('yts')->describedAs('Search YTS (always on terminal)')->boolean()->defaultsTo(false);
	$opts->option('t')->aka('onterminal')->describedAs('Parse results on terminal')->boolean()->defaultsTo(false);

	$numArgs=0;
	$argSearch="";
	$cmd="";
	$o="";
	for ($i=0;$i<=15;$i++){
		$argSearch.=$opts[$i]."+";
		if ($opts[$i]=="")
			break;
		// echo "$i:".$opts[$i].PHP_EOL;
		$numArgs=$i+1;
	}
	if ($numArgs==0){
		echo "You have to search something ... try some keywords after the command";
		fwrite(STDERR, PHP_EOL);
		exit(5);
	}
	$argSearch = trim($argSearch, "+ ");
	if ($opts['man'] === true){
		echo MAN;
		fwrite(STDERR, PHP_EOL);
		exit(0);
	}
//////
	if ($opts['onterminal'] === true){
		// parse on terminal
		if ($opts['google'] === true){
			$o.= "not implemented";
			show("$o", 1);
		}		
		if ($opts['playstore'] === true){
			$o.= "not compatible -t and -p together";
			show("$o", 2);
		}
		if ($opts['hackernews'] === true){
			$o.= "not yet implemented";
			show("$o", 3);
		}
		if ($opts['piratebay'] === true){
			$o.= "not yet implemented";
			show("$o", 3);
		}
		if ($opts['yts'] === true){
			$o.= "not yet implemented";
			show("$o", 3);
		}
	
		if ($opts['searxme'] === true){
			$o .= "not yet implemented";
			show("$o", 3);
		}
				
		if ($opts['duck'] === true){
			$cmd = "curl 'https://duckduckgo.com/?q=$argSearch&format=json'";
			$o = exeout($cmd);
		}
		
	}else{
		// normal behavior
		if ($opts['google'] === true){
			$cmd = "termux-open 'https://www.google.com/search?q=$argSearch'";
			$o = exeout($cmd);
		}		
		if ($opts['duck'] === true){
			$cmd = "termux-open 'https://www.duckduckgo.com/?q=$argSearch'";
			$o = exeout($cmd);
		}		
		if ($opts['searx'] === true){
			$cmd = "termux-open 'https://searx.me/search?q=$argSearch'";
			$o = exeout($cmd);
		}		
		if ($opts['playstore'] === true){
			$cmd = "termux-open 'http://play.google.com/store/search?q=$argSearch&c=apps'";
			$o = exeout($cmd);
		}
		if ($opts['hackernews'] === true){
			$cmd = "termux-open 'https://hn.algolia.com/?sort=byPopularity&prefix&page=0&dateRange=all&type=story&query=$argSearch'";
			$o = exeout($cmd);
		}
		if ($opts['piratebay'] === true){
			$o.="insecure search on javascript enabled browser...exiting";
			show("$o", 4);
		}
		if ($opts['yts'] === true){
			$o.="insecure search on javascript enabled browser...exiting";
			show("$o", 4);
		}
	
		// if ($opts['searxme'] === true) // this is default
		{
			$cmd = "termux-open 'https://searx.me/search?q=$argSearch'";						
			$o = exeout($cmd);
			exit(0);
		}		
	}


