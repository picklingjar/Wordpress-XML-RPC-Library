<?php
/*
Copyright (c) 2011, The Pickling Jar Ltd <code@thepicklingjar.com>

Permission to use, copy, modify, and/or distribute this software for any
purpose with or without fee is hereby granted, provided that the above
copyright notice and this permission notice appear in all copies.

THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
*/

function wordpress_edit_post($xmlrpcurl, $postid, $username, $password, $title, $content, $categories, $excerpt, $text_more, $keywords, $pingurls, $date_created, $customfields = '', $publish=1, $proxyipports = ""){
	global $globalerr;
	$client = new xmlrpc_client($xmlrpcurl);
    $client->setSSLVerifyPeer(false);
	$params[] = new xmlrpcval($postid);
	$params[] = new xmlrpcval($username);
	$params[] = new xmlrpcval($password);
        if($categories != ""){
                if(is_array($categories)){
                        $cat = array();
                        foreach($categories as $category){
                                if($category != ""){
                                        $cat[] = new xmlrpcval($category, "string");
                                }
                        }
                }
                else {
                        $cat = array(new xmlrpcval($categories, "string"));
                }
        }
        else {
                $cat = array();
        }
        $cf = array();
        if($customfields != ''){
                if(is_array($customfields)){
                        $cf = php_xmlrpc_encode($customfields);
                }
        }

	$rpcstruct= new xmlrpcval(
		array(
			"title" => new xmlrpcval($title, "string"),
			"description" => new xmlrpcval($content, "string"),
                        "categories" => new xmlrpcval($cat,"struct"),
			"mt_excerpt" => new xmlrpcval($excerpt, "string"),
			"mt_text_more" => new xmlrpcval($text_more, "string"),
			"mt_keywords" => new xmlrpcval($keywords, "string"),
			"mt_tb_ping_urls" => new xmlrpcval($pingurls, "string"),
			"dateCreated" => new xmlrpcval($date_created, "dateTime.iso8601"),
                        "custom_fields" => $cf,
  		),
		"struct");
	$params[] = $rpcstruct;
	$params[] = new xmlrpcval($publish,'boolean');
	$msg = new xmlrpcmsg("metaWeblog.editPost",$params);
        if(is_array($proxyipports)){
                $proxyipport = $proxyipports[array_rand($proxyipports)];
        }
        elseif($proxyipports != ""){
                $proxyipport = $proxyipports;
        }
        else {
                $proxyipport = "";
        }
        if($proxyipport != ""){
                if(preg_match("/@/", $proxyipport)){
                        $proxyparts = explode("@", $proxyipport);
                        $proxyauth = explode(":",$proxyparts[0]);
                        $proxyuser = $proxyauth[0];
                        $proxypass = $proxyauth[1];
                        $proxy = explode(":", $proxyparts[1]);
                        $proxyip = $proxy[0];
                        $proxyport = $proxy[1];
                        $client->setProxy($proxyip, $proxyport, $proxyuser, $proxypass);
                }
                else {
                        $proxy = explode(":",$proxyipport);
                        $proxyip = $proxy[0];
                        $proxyport = $proxy[1];
                        $client->setProxy($proxyip, $proxyport);
                }
        }
	$r = $client->send($msg);
	if($r === false){
                $globalerr = "XMLRPC ERROR - Could not send xmlrpc message";
		return(false);
	}
	if (!$r ->faultCode()) {
		return(php_xmlrpc_decode($r->value()));
	}
	else {
                $globalerr = "XMLRPC ERROR - Code: " . htmlspecialchars($r->faultCode()) . " Reason: '" . htmlspecialchars($r->faultString()). "'";
	}
	return(false);
}
?>
