<?php

# CatPress
#
# A really dumb way to make a webpage
#

$bodies=array();
$testing=false;

function process($in){

	global $pageroot;
	global $pagedomain;


	global $bodies;
	global $prefpage;


	$out = str_replace('^pageroot^', $pagedomain.$pageroot, $in);


	while ($ls = strpos($out, "^link^",$ls+1)){

		$le = strpos($out, "^&", $ls);

		if ($le) {

			$ld = substr($out, $ls,$le-$ls);


			$lds = explode ("^", $ld);

			$lu = $lds[2];
			$lde = $lds[3];

			$ld = "<a href=\"$lu\">$lu</a> : $lde";


			$out = substr($out, 0, $ls-1) . "$ld" . substr($out, $le+2);

		}

	}

	//deal with page links
	$c = count($bodies);
	if ($c >0) { 


		if ($bodies[0] != $prefpage)				
			$oldest = "page-". $bodies[0];


		if ($bodies[$c-1] != $prefpage)
			$newest = "page-". $bodies[$c-1];


		if ($prefpage!=-1){
			for ($i=0;$i<$c;$i++) {
				//echo " bodies ".  $i. "is " . $bodies[$i]; 
				if ($bodies[$i] == $prefpage){

					//echo " [i is $i, c is $c]\n\n";
					if ($i>0)
						$next = "page-". $bodies[$i-1];
					if ($i< ($c-1) ) 
						$prev = "page-". $bodies[$i+1];
				}

			}

		}


		$out .="<center>";
		if ($newest)
			$out .= "|<a href=\"$newest\">Newest</a>|";
		else
			$out .= "[<i>viewing Newest</i>]";




		if ($prev)
			$out .= " . . . <a href=\"$prev\">|&lt;&lt;Newer|</a> . . . ";



		if ($next)
			$out .= " . . . <a href=\"$next\">|Older &gt;&gt;|</a> . . . ";
	


	
		if ($oldest)
			$out .= "|<a href=\"$oldest\">Oldest</a>|";
		else
			$out .="[<i>viewing Oldest</i>]";


		$out .="<br/><a href=\"page-all\"><i>(view all as one document)</i></a></center>";



	}


	



	return $out;	
}

if (isset($_SERVER['HTTPS']))
	$encrypted = 's';
else
	$encrypted = '';

$pagedomain ="http$encrypted://mwsherman.com";
$pageroot='/catpress-example';
$pagepart=explode('/', $pageroot);

$p = getcwd();
$uri = $_SERVER['REQUEST_URI'];

if(substr($uri,-1)!='/')
	$uri.='/';

$uripart = explode('/', $uri);

for ($i=0;$i<count($pagepart);$i++) {
	//echo "$i: " . $pagepart[$i] . " vs " . $uripart[0] . "\n";
	if ($pagepart[$i] != $uripart[0]) {
		echo "No sense\n";
		exit(0);
	}
	array_shift($uripart);
}
if ($uripart[0] == 'test') {
	array_shift($uripart);
	$testing=true;

}

$prefpage=-1;
$c = count($uripart);
if (($c> 2) ) {
	if (substr($uripart[$c-2],0,5) == "page-"){

		$prefpage = substr($uripart[$c-2],5);
		unset($uripart[$c-1]);
		$uripart[$c-2] = "";
	}

		
}
	

$path =  implode('/', $uripart);

//echo "path is $path\n";

$topheader = process (file_get_contents('header'));

$newheader = process (file_get_contents($path.'header'));

if(strlen($newheader))
	$topheader = $newheader;

$footer = process (file_get_contents('footer'));
$title = process (file_get_contents($path."title"));

$bodyraw = false;




//see if we have a directory

$dira = scandir("$path");
foreach ($dira as $d) {
	if (substr($d,0,4) == "body"   && strlen($d)>4 )
		$bodies[]= (int) substr($d,4);
}


sort($bodies, SORT_NUMERIC);



if ($prefpage == "all") {
	$bodyraw = file_get_contents($path."body");
	if($bodyraw === false)
		$bodyraw = "";

	//for ($i=count($bodies)-1;$i>=0;$i--) {
	for($i=0;$i<count($bodies);$i++){

		$b = file_get_contents($path."body".$bodies[$i]);
		if ($b !== false) 
			$bodyraw .= "<hr/>" . $b;
	}

	$bodies=array();
}


if ($prefpage == -1 || $prefpage== $bodies[count($bodies)-1])  //if user didn't specify page, assume there might be a body 
	$bodyraw = file_get_contents($path."body");




if (count($bodies) > 1 && $bodyraw !== false) {
	$prefpage = $bodies[count($bodies)-1];
	$prebody = process ($bodyraw) ."<hr/>";
	$bodyraw=false;
	
}

if ($bodyraw === false) {



	if (count($bodies)==0) {
		header("HTTP/1.1 404");
		$bodyraw = file_get_contents('404');

	}
	else {

		if ($prefpage==-1) {
			$prefpage = $bodies[count($bodies)-1];
		}

		//echo "prefpage is $prefpage >   $path body $prefpage";




		$bodyraw = file_get_contents($path."body$prefpage");

		if ($bodyraw === false) {
			header("HTTP/1.1 404");
			$bodyraw = file_get_contents('404');

		}
		

	}

} 





$body = process ($bodyraw);

$style = file_get_contents('style');
# spit it all out
echo '<html>';
echo "<head>";
echo "<style>

$style
</style>";

echo '<meta name="viewport" content="width=device-width, initial-scale=1">';

echo "<title>" . htmlspecialchars($title) . "</title></head>\n";
echo '<body>';
echo "$topheader";
echo '<div class="page"  >';
echo '<div class="innerpage"  >';
echo "<h1>$title</h1>";
echo $prebody;
echo $body;
echo "</div>";
echo "</div>";
echo "<div class=\"bumper\"></div>";



if ($testing){
	$dira = scandir(".");
	$dir = implode(" ", $dira);
	echo "<div class=\"test\">THIS IS TEST $path \n$dir</div>";
}
echo "<div class=\"footer\">$footer</div>";

echo "</body>\n";

echo "</html>";
# ok that's about it

