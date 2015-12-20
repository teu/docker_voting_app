<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

require_once __DIR__.'/../vendor/autoload.php';

define('VOTE_UBUNTU',1);
define('VOTE_WINDOWS',0);

$app = new Silex\Application();
$app['debug']=true;

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__.'/../views',
]);

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => [
        'driver'   => 'pdo_mysql',
	'dbname' => 'app',
	'host' => 'db',
	'password' => 'appPass',
	'port' => 3306,
	'user' => 'app'
    ]
));

$getVotes = function() use ($app){
	$sql = "SELECT vote, COUNT(vote) as 'count' FROM votes GROUP BY vote";
        $votesRaw = $app['db']->fetchAll($sql, []);
	
	$votes = ['ubuntu'=>0, 'windows'=>0];
	$overall = 0;
	foreach($votesRaw as $k=>$v){
		if($v['vote'] == VOTE_UBUNTU){
			$votes['ubuntu'] = $v['count'];
		}
		if($v['vote'] == VOTE_WINDOWS){
			$votes['windows'] = $v['count'];
		}
		$overall += $v['count']; 
	}
	
	$percentages = ['ubuntu' => round(($votes['ubuntu']/$overall*100),1), 'windows'=>round(($votes['windows']/$overall*100),1)];
	return $percentages;
};

$hasVoted = function(Request $request){
	$cookies = $request->cookies;
        return $cookies->has("votedAlready");
};

$app->get('/', function() use ($app) {    	
	return $app['twig']->render('index.twig', []);
});

$app->get('/votes', function(Request $request) use ($app, $getVotes, $hasVoted){
	return $app['twig']->render('votes.twig',['votes'=>$getVotes(), 'votedAlready'=>$hasVoted($request)]);
});

$app->post('/votes', function(Request $request) use ($app, $hasVoted){
	if(true === $hasVoted($request)){
		return $app['twig']->render('error.twig', ['msg'=>'Already voted']);
	}

	$param = $request->get('vote');
	if(in_array($param,["vote Ubuntu","vote Windows"])){
		$sql = "INSERT INTO votes (vote) VALUES (?)";
		switch($param){
			case "vote Ubuntu":
				$insert = VOTE_UBUNTU;
				break;
			case "vote Windows":
				$insert = VOTE_WINDOWS;
				break;
		}	
		$app['db']->executeUpdate($sql, [$insert]);
		
		$cookie = new Cookie('votedAlready',1);	
		$response = new RedirectResponse('/votes');
		$response->headers->setCookie($cookie);
		return $response;
	}else{
		return $app['twig']->render('error.twig', ['msg'=>'Something seams to be wrong with the request']);
	}
	
});

$app->run();
