<?php
header( 'Content-type: text/plain' );
header( 'Link: <https://textplain.blog/feed>; rel="alternate"; type="application/rss+xml"' );

// Posts go in the _posts/ folder and must have filenames of the syntax "0000-00-00 post-slug.txt"; first line inside the file is the title
$posts = array_reverse( preg_grep( '/\d{4}-\d{2}-\d{2} [a-z0-9_-]+\.txt/', glob( '_posts/*.txt' ) ) );

// Check for homepage request:
if( '/' === $_SERVER['REQUEST_URI'] ) {
  echo "TEXT/PLAIN BLOG\n===============\n\nAn experimental blog. Because who said that blogs had to be written in HTML?\n\nPosts\n-----\n\n";
  foreach( $posts as $post ) {
    preg_match( '/(\d{4}-\d{2}-\d{2}) ([a-z0-9_-]+)\.txt/', $post, $matches );
    echo "$matches[1]: https://textplain.blog/$matches[2]\n";
  }
  echo "\nSubscribe\n---------\n\nRSS feed: https://textplain.blog/feed\n";
  echo "\nAbout\n-----\n\nThis project was created by Dan Q <https://danq.me/>. You can blame him.\n";
  exit;
}

// Check for RSS feed request:
if( preg_match( '/^\/feed\/?$/', $_SERVER['REQUEST_URI'] ) ) {
  header( 'Content-type: application/rss+xml' );
  echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">\n<channel>\n  <title>text/plain blog</title>\n  <description>An experimental blog delivered in text/plain only.</description>\n  <link>https://textplain.blog/</link>\n  <atom:link href=\"https://textplain.blog/feed\" rel=\"self\" type=\"application/rss+xml\" />\n";
  foreach( $posts as $post ) {
    $description = htmlspecialchars( htmlspecialchars( file_get_contents( $post ) ) ); // note double-encoding to ensure RSS readers don't misinterpret any HTML
    preg_match( '/\d{4}-\d{2}-\d{2} ([a-z0-9_-]+)\.txt/', $post, $matches );
    $url = 'https://textplain.blog/' . $matches[1];
    echo "  <item>\n    <description>$description</description>\n    <link>$url</link>\n    <guid isPermaLink=\"true\">$url</guid>\n  </item>\n";
  }
  echo "</channel>\n</rss>";
  exit;
}

// Check for post request:
if( preg_match( '/^\/([a-z0-9_-]+)(\/|\.txt)?$/', $_SERVER['REQUEST_URI'], $matches ) ) {
  $matching_posts = preg_grep( "/\d{4}-\d{2}-\d{2} {$matches[1]}+\.txt/", $posts );
  if( count( $matching_posts ) == 1 ) {
    readfile( $matching_posts[0] );
    echo "\n----------------------------------------------------------------------------------------------------\nThis post appeared on https://textplain.blog/";
    exit;
  }
}

// Failing all the above, return 404
header( 'HTTP/1.0 404 Not Found' );
echo "404: Not Found\n==============\n\nThe requested URL was not found on the server.\n\nGiven that you probably had to enter the URL manually, please check your spelling and try again.\n\nOr go to https://textplain.blog/ and browse from there.";
