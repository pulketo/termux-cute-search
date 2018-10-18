<?php
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
	require __DIR__ ."/../vendor/autoload.php";
	$opts = new Commando\Command();

// Functions
	function fix($i){
		$t = array(
		  "\u00c0" =>"À",     "\u00c1" =>"Á",     "\u00c2" =>"Â",     "\u00c3" =>"Ã",     "\u00c4" =>"Ä",     "\u00c5" =>"Å",     "\u00c6" =>"Æ",     "\u00c7" =>"Ç",     "\u00c8" =>"È",     "\u00c9" =>"É",     "\u00ca" =>"Ê",     "\u00cb" =>"Ë",     "\u00cc" =>"Ì",     "\u00cd" =>"Í",     "\u00ce" =>"Î",     "\u00cf" =>"Ï",     "\u00d1" =>"Ñ",     "\u00d2" =>"Ò",     "\u00d3" =>"Ó",     "\u00d4" =>"Ô",     "\u00d5" =>"Õ",     "\u00d6" =>"Ö",     "\u00d8" =>"Ø",     "\u00d9" =>"Ù",     "\u00da" =>"Ú",     "\u00db" =>"Û",     "\u00dc" =>"Ü",     "\u00dd" =>"Ý",     "\u00df" =>"ß",     "\u00e0" =>"à",     "\u00e1" =>"á",     "\u00e2" =>"â",     "\u00e3" =>"ã",     "\u00e4" =>"ä",     "\u00e5" =>"å",     "\u00e6" =>"æ",     "\u00e7" =>"ç",     "\u00e8" =>"è",     "\u00e9" =>"é",     "\u00ea" =>"ê",     "\u00eb" =>"ë",     "\u00ec" =>"ì",     "\u00ed" =>"í",     "\u00ee" =>"î",     "\u00ef" =>"ï",     "\u00f0" =>"ð",     "\u00f1" =>"ñ",     "\u00f2" =>"ò",     "\u00f3" =>"ó",     "\u00f4" =>"ô",     "\u00f5" =>"õ",     "\u00f6" =>"ö",     "\u00f8" =>"ø",     "\u00f9" =>"ù",     "\u00fa" =>"ú",     "\u00fb" =>"û",     "\u00fc" =>"ü",     "\u00fd" =>"ý", "\u00ff" =>"ÿ", "\u202a"=>"", "\u202c"=>"");
		return strtr($i, $t);
		}


	function show($o){
		global $opts, $argSearch;
		$where = ($opts['onterminal']===true)?"terminal":"default";
		$out = array("action"=>"search", "where"=>"$where", "search"=>explode("+", $argSearch) ); 
		echo json_encode($out, JSON_PRETTY_PRINT);
	}

	function exeout($cmd){
		global $opts, $argSearch;
		$where = ($opts['onterminal']===true)?"terminal":"default";
		$exe = trim([`$cmd && echo OK || echo NOK`]);
		$out = array("action"=>"search", "where"=>"$where", "search"=>explode("+", $argSearch), "cmd"=>"$cmd", "exe"=>"$exe" ); 
		echo json_encode($out, JSON_PRETTY_PRINT);
		exit(0); //temp
	}

	$opts->option('m')->aka('man')->describedAs(MAN)->boolean()->defaultsTo(false);
	$opts->option('p')->aka('playstore')->describedAs('Search the play store for keywords')->boolean()->defaultsTo(false);
	$opts->option('x')->aka('searxme')->describedAs('use searx.me for keywords')->boolean()->defaultsTo(false);
	$opts->option('g')->aka('google')->describedAs('Search google for keywords')->boolean()->defaultsTo(false);
	$opts->option('d')->aka('duck')->describedAs('Search duck duck go for keywords')->boolean()->defaultsTo(false);
	$opts->option('n')->aka('hackernews')->describedAs('Search Hacker News for keywords')->boolean()->defaultsTo(false);
	$opts->option('b')->aka('piratebay')->describedAs('Search the Pirate Bay (always on terminal)')->boolean()->defaultsTo(false);
	$opts->option('y')->aka('yts')->describedAs('Search YTS (always on terminal)')->boolean()->defaultsTo(false);
//	$opts->option('t')->aka('onterminal')->describedAs('Parse results on terminal')->boolean()->defaultsTo(false);

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
			$o.= "not yet implemented";
			show("$o", 3);
		}
		
	}else{
		// normal behavior
		if ($opts['google'] === true){
			$cmd = "termux-open 'https://www.google.com/search?q=$argSearch'";
			exeout($cmd);
		}		
		if ($opts['duck'] === true){
			$cmd = "termux-open 'https://www.duckduckgo.com/?q=$argSearch'";
			exeout($cmd);
		}		
		if ($opts['searx'] === true){
			$cmd = "termux-open 'https://searx.me/search?q=$argSearch'";
			exeout($cmd);
		}		
		if ($opts['playstore'] === true){
			$cmd = "termux-open 'http://play.google.com/store/search?q=$argSearch&c=apps'";
			exeout($cmd);
		}
		if ($opts['hackernews'] === true){
			$cmd = "termux-open 'https://hn.algolia.com/?sort=byPopularity&prefix&page=0&dateRange=all&type=story&query=$argSearch'";
			exeout($cmd);
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
			exeout($cmd);
			exit(0);
		}		
	}


