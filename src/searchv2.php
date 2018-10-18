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
			$this->query .= "$term+";
		}
		
		public function resetSearchTerms(){
			$this->query = null;
		}

		public function google($num=10, $start=0, $ssl=true){
			$o = $this->sURI("www.google.com/search?q=", $this->query, "&num=$num&start=$start&safe=off", $ssl);
			return $o;
		}
		public function duckduckgo($num=10, $start=0, $ssl=true){
			$o = $this->sURI("api.duckduckgo.com/?q=", $this->query, "&n=$num&s=$start&t=termux-cute-search-dev", $ssl);
			return $o;
		}
		public function searxme($num=10, $start=0, $ssl=true){
			$page = ($start / $num) + 1;
			$o = $this->sURI("searx.me/search?q=", $this->query, "&pageno=$page&safesearch=None", $ssl);
			return $o;
		}
		public function playstore($num=10, $start=0, $ssl=true){
			$o = $this->sURI("play.google.com/store/search?q=", $this->query, "&c=apps", $ssl);
			return $o;
		}

		private function sURI($uri, $query, $options=null, $ssl=true){
			if (strlen ($this->query) < 2)
				return false;
			$proto = ( $ssl === true ) ? "https" : "http"; 
			$o = $proto."://".$uri.$query.$options;
			return $o;
		}
		
	}
/////////////////////////
	include __DIR__."/man.php";
	require __DIR__ ."/../vendor/autoload.php";
	$opts = new Commando\Command();
//	$opts->option('m')->aka('man')->describedAs(MAN)->boolean()->defaultsTo(false);
	$opts->option('e')->aka('engine')->describedAs('--engine <...> or -e <google|duckgo|searxme|playstore> or  -e <g|d|s|p>')->defaultsTo("s");
	//hackernews,piratebay,yts	
	$mySearch = new termuxSearch();	
	$numArgs=0;
	for ($i=0;$i<=15;$i++){
		$mySearch->addSearchTerms($opts[$i]);		
		if ($opts[$i]=="" || $opts[$i]==null )
			break;
		// echo "$i:".$opts[$i].PHP_EOL;
		$numArgs=$i+1;
	}
	if ($numArgs==0){
		echo "You have to search something ... try some keywords after the command";
		fwrite(STDERR, PHP_EOL);
		exit(5);
	}
	switch($opts['engine']){
		case "searx":
		case "searxme":
		case "s":
			$t = $mySearch->searxme();
			break;
		case "google":
		case "g":
			$t = $mySearch->google();
			break;
		case "playstore":
		case "play":
		case "p":
			$t = $mySearch->playstore();
			break;
		case "duckduckgo":
		case "duckgo":
		case "d":
			$t = $mySearch->duckduckgo();
			break;
		default:
			die("no engine was selected").PHP_EOL;			
	}
	$c = trim(`termux-open '$t'`);
